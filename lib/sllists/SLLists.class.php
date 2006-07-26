<?php
/*
* This class implements a PHP wrapper around the scriptaculous javascript libraries created by
* Thomas Fuchs (http://script.aculo.us/).
*
* SLLists was created by Greg Neustaetter in 2005 and may be used for free by anyone for any purpose.  Just keep my name in here please and
* give me credit if you like, but give Thomas all the real credit!
*/
class SLLists {

	var $lists;
	var $jsPath;
	var $debug;
	
	function SLLists($jsPath) {
		$this->lists = array();
		$this->jsPath = $jsPath;
		$this->debug = false;
	}
	
	function addList($list, $input, $tag = 'li', $additionalOptions = '') {
		if ($additionalOptions != '') $additionalOptions = ','.$additionalOptions;
		$this->lists[] = array("list" => $list, "input" => $input, "tag" => $tag, "additionalOptions" => $additionalOptions);
	}
	
	function printTopJS() {
		?>
		<script src="<?php echo $this->jsPath;?>/prototype.js" type="text/javascript"></script>
		<script src="<?php echo $this->jsPath;?>/scriptaculous.js" type="text/javascript"></script>
		<?php
	}
	
	function printJSFunction()
	{
		$result = '';
		foreach($this->lists as $list) {
			$result .= 'document.getElementById(\''.$list['input'].'\').value = Sortable.serialize(\''.$list['list'].'\');';
		}
		return $result;
	}
	
	function printBottomJs() {
		?>
			<?php
			foreach($this->lists as $list) {
				?>
				Sortable.create('<?php echo $list['list'];?>',{tag:'<?php echo $list['tag'];?>'<?php echo $list['additionalOptions'];?>});
				<?php
			}
			?>
		<?php
	}
	
	function printHiddenInputs() {
		$inputType = ($this->debug) ? 'text' : 'hidden';

		foreach($this->lists as $list) {
			if ($this->debug) echo '<br />'.$list['input'].': ';
			?>
			<input type="<?php echo $inputType;?>" name="<?php echo $list['input'];?>" id="<?php echo $list['input'];?>" size="60">
			<?php
		}
		if ($this->debug) echo '<br />';
	}
	
	function printForm($action, $method = 'POST', $submitText = 'Submit', $submitClass = '', $formName = 'sortableListForm', $cancelText = 'Cancel', $listcontent = '') {
		?>
		<form action="<?php echo $action;?>" method="<?php echo $method;?>" onsubmit="<?php echo $this->printJSFunction(); ?>xajax_reorder_process(xajax.getFormValues('<?php echo $formName;?>'));return false;" name="<?php echo $formName;?>" id="<?php echo $formName;?>">
			<input type="submit" value="<?php echo $submitText ?>" class="<?php echo $submitClass;?>" />
			<input type="button" value="<?php echo $cancelText; ?>" class="<?php echo $submitClass;?>" onclick="xajax_content_list_ajax(); return false;" />
			<?php echo $listcontent ?>
			<?php $this->printHiddenInputs();?>
			<input type="hidden" name="sortableListsSubmitted" value="true" />
			<?php
			if ($this->debug) {
				?><input type="button" value="View Serialized Lists" class="<?php echo $submitClass;?>" onClick="populateHiddenVars();" /><br /><?php
			}
			?>
			<input type="submit" value="<?php echo $submitText ?>" class="<?php echo $submitClass;?>" />
			<input type="button" value="<?php echo $cancelText; ?>" class="<?php echo $submitClass;?>" onclick="xajax_content_list_ajax(); return false;" />
		</form>
		<?php
	}
	
	function getOrderArray($input,$listname,$itemKeyName = 'element',$orderKeyName = 'order') {
		parse_str($input,$inputArray);
		$inputArray = $inputArray[$listname];
		$orderArray = array();
		for($i=0;$i<count($inputArray);$i++) {
			$orderArray[] = array($itemKeyName => $inputArray[$i], $orderKeyName => $i +1);
		}
		return $orderArray;
	}

}