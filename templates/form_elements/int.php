<div class="form-group">
	<input type="text"<?= $this->isEndTime()  ? " readonly" : ""?>
		   class="form-control moneyField js-input" name="answer" value="<?= $this->getValue()?>">
</div>