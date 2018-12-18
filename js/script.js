$(function () {

	if($("h1")[0]){
		// таймер
		function CountDownTimer(duration, granularity) {
			this.duration = duration;
			this.granularity = granularity || 1000;
			this.tickFtns = [];
			this.running = false;
		}
		CountDownTimer.prototype.start = function() {
			if (this.running) {
				return;
			}
			this.running = true;
			var start = Date.now(),
				that = this,
				diff, obj;

			(function timer() {
				diff = that.duration - (((Date.now() - start) / 1000) | 0);

				if (diff > 0) {
					setTimeout(timer, that.granularity);
				} else {
					diff = 0;
					that.running = false;
				}
				obj = CountDownTimer.parse(diff);
				that.tickFtns.forEach(function(ftn) {
					ftn.call(this, obj.minutes, obj.seconds);
				}, that);
			}());
		};
		CountDownTimer.prototype.onTick = function(ftn) {
			if (typeof ftn === 'function') {
				this.tickFtns.push(ftn);
			}
			return this;
		};
		CountDownTimer.prototype.expired = function() {
			return !this.running;
		};
		CountDownTimer.parse = function(seconds) {
			return {
				'minutes': (seconds / 60) | 0,
				'seconds': (seconds % 60) | 0
			};
		};

		var display1 = document.querySelector('#timeAll'),
			timer1 = new CountDownTimer($("#timeAll").data("time"));

		timer1.onTick(format1).start();

		function format1(minutes, seconds) {
			minutes = minutes < 10 ? "0" + minutes : minutes;
			seconds = seconds < 10 ? "0" + seconds : seconds;
			display1.textContent = minutes + ':' + seconds;
		}
		var inputs = document.querySelectorAll('.js-input');
		// установка счетчиков
		inputs.forEach(function (item) {
			var cleave = new Cleave(item, {
				numeral: true,
				numeralDecimalMark: '.',
				delimiter: ''
			});
		});
		// если есть таймер
		if($("#time")[0]) {
			var display2 = document.querySelector('#time'),
				timer2 = new CountDownTimer($("#time").data("time"));
			timer2.onTick(format2).start();
			function format2(minutes, seconds) {
				minutes = minutes < 10 ? "0" + minutes : minutes;
				seconds = seconds < 10 ? "0" + seconds : seconds;
				display2.textContent = minutes + ':' + seconds;
			}
		}
		// получаем текущий урл
		var url = window.location.href;
		url = url.substring(0, url.lastIndexOf("/") + 1);
		// проверка тестирования
		var interval = setInterval(function(){
			$.ajax({
				type: "GET",
				async: true,
				dataType: "json",
				url: url+"/ajax.php",
				success : function(json){
					// если время тестирования вышло
					if( json.endAll ){
						clearInterval(interval);
						$("#status").html('<div class="mt-3">' +
							'<a href="'+url+'success.php" class="btn btn-primary" >Завершить тест</a>' +
							'</div>');
						$("#questionBox").addClass("alert-danger").html('<div class="alert">Время отведенное на тест вышло</div>');
						$("#answer").find("textarea,input").prop("disabled", true);
						$(".nav").hide()
					// если вышло время ответа на вопрос
					}else if( json.endQuestion ){
						$("#questionBox").html('Время отведенное на вопрос вышло,<br> выберите вопросы из списка');
						$("#answer").find("textarea,input").prop("disabled", true);
					}
					// список
					if( json.overList != null ){
						var overListCnt = json.overList.length;
						if( overListCnt > 0 ){
							for ( var i = 0; i < overListCnt; i++ ){
								var elem = $("#nav li a[href*='"+json.overList[i]+"']");
								if( elem[0] ){
									if( !elem.find(".status")[0] ){
										elem.prepend("<span class='status'> &otimes; </span>")
									}
								}
							}
						}
					}
				}
			})
		}, 1000);
	}

});
