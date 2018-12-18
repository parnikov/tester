<div class="row alert nav">
	<div class="col-12">
		Вопросы
	</div>
</div>
<?php
if( self::$navCountItems ) {
	?>
	<nav class="nav" id="nav">
		<ul class="pagination pagination-lg">
		<?php
		$cnt = 1;
		foreach ( self::$navItems as $key => $item) {
			?>
			<li class="page-item<?=( \Tester\StackQuestions::getCurId() == $key ) ? " active" : ""?>">
				<a class="page-link" href="<?=$_SERVER["PHP_SELF"]?>?id=<?=$key?>">
					<?php
					if(\Tester\StackQuestions::isEndTimeQuestion($key)){
					echo "<span class='status'> &otimes; </span>";
					}
					echo $cnt++;
					if( \Tester\StackQuestions::isDone($key) ){
						echo "<span>	&Xi;</span>";
					}
					?>
				</a>
			</li>
			<?php
		}
		?>
		</ul>
	</nav>
	<?php
}