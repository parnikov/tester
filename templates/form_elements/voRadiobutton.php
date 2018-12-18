<?php
if(!empty($this->getAns())){
	foreach ( $this->getAns() as $key => $answer){
		$checkStatus = "";
		if( is_array( $this->getValues() ) && in_array( $key, $this->getValues() ) ){
			$checkStatus = " checked";
		}
		?><div class="form-check">
			<input class="form-check-input" type="radio"<?=( $this->isEndTime() ) ? " disabled" : ""?> value="<?=$key?>"
				   id="defaultCheck_<?=$key?>"<?=$checkStatus?> name="answer[]">
			<label class="form-check-label" for="defaultCheck_<?=$key?>">
				<?=$answer?>
			</label>
		</div><?php
	}
}
