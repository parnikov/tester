<?php
if(!empty($this->getAns())){?>
	<?php
	foreach ( $this->getAns() as $key => $answer){
		$checkStatus = "";
		if( is_array($this->getValues()) && in_array($key, $this->getValues())){
			$checkStatus = " checked";
		}
		?>
		<div class="form-check">
			<input class="form-check-input"<?=( $this->isEndTime() ) ? " disabled " : ""?>type="checkbox" value="<?=$key?>" id="defaultCheck_<?=$key?>"<?=$checkStatus?> name="answer[]">
			<label class="form-check-label" for="defaultCheck_<?=$key?>">
				<?=$answer?>
			</label>
		</div>
	<?}?>
<?php }