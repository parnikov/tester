<?php
/**
 * Created by PhpStorm.
 * User: a.parnikov
 * Date: 11/22/18
 * Time: 1:06 AM
 */
if(gettype($this) == "object"){?>
	<textarea name="answer"  <?=( $this->isEndTime() ) ? " readonly" : ""?> class="form-control" ><?=$this->getValue()?></textarea>
<?php }

