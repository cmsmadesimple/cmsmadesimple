<?php
/*
 * sequence
 *
 * a sequence is a sequence of pages (most likely forms but not necessarily) which have to be viewed in sequence
 *
 * the sequence is defined at the outset and stored as a static variable
 * if a page is viewed out of sequence the user is taken to the step they should be on
 * the sequence static is in the session
 * if a form does not receive a seqstep variable the sequence dies and the user is taken to the panic page
 * in debug mode such errors are displayed for sanity checking
 *
 *
 */

/*
 *
 * setSequence($name, $actions, $panic, $active)
 * $name = whatever you want to call this sequence
 * $actions = array of strings of names of actions to be called or comma separated list of actionnames
 * $panic = panic action
 * $active = boolean - is sequence to be active?
 *
 */
	function setSequence($name, $actions, $panic, $active) {
		$newSeq = new Sequence();
		$actionsarr = array ();
		if (is_array($actions)) {
			$actionsarr = $actions;
		} else {
		 	$actionsarr = split(',', $actions);
		 	for ($i=0; $i<count($actionsarr); $i++) {
		 		$actionsarr[i] = trim ($actionsarr[i]);
		 	}
		}
		$newSeq->name = $name;
		$newSeq->actions = $actionsarr;
		$newSeq->panic = $panic;
		$newSeq->active = $active;
		$newSeq->Save();
	}

/*
 *
 * getSequence($id)
 * $id = id or name of sequence
 *
 */
	function getSequence($id) {
		return SequenceOperations::LoadSequenceByID($id);
	}

/*
 *
 * getHasNextStep($sequence)
 * $sequence is the current sequence.
 * returns true if currentStep+1 is less than the length of $actions
 *
 */
	function getHasNextStep($sequence) {
		$thisStep = $sequence->currentStep;
		$sequenceLength = count($sequence->actions);
		return ($thisStep < ($sequenceLength-1));
	}

/*
 *
 * getNextStep($sequence)
 * returns actionname for next step in sequence and increments currentStep by 1
 *
 */
	function getNextStep($sequence) {
		if (checkIsOK($sequence)) {
			if (getHasNextStep($sequence)) {
				$sequence->currentStep++;
				$_SESSION['seqStep']++;
			}
			return $sequence->actions[$sequence->currentStep];
		} else {
			$panic = $sequence->panic;
			killSequence($sequence);
			return $panic;
		}
		
	}
/*
 *
 * getCurrentStep($sequence)
 * returns current step in sequence
 *
 */
	function getCurrentStep($sequence) {
		if (checkIsOK($sequence)) {
			return $sequence->currentStep;
		} else {
			$panic = 0;
			killSequence($sequence);
			return $panic;
		}
	}
	
/*
 *
 * getCurrentStepaction($sequence)
 * returns action for current step in sequence
 *
 */
	function getCurrentStepAction($sequence) {
		if (checkIsOK($sequence)) {
			return $sequence->actions[$sequence->currentStep];
		} else {
			$panic = $sequence->panic;
			killSequence($sequence);
			return $panic;
		}
	}
 /*
 *
 * killSequence($sequence)
 * removes seqStep from the session
 * sets $sequence to null
 *
 */
	function killSequence($sequence) {
		$sequence = null;
		unset($_SESSION['seqId']);
		unset($_SESSION['seqStep']);
	} 

 /*
 *
 * checkIsOk()
 * gets seqStep from session and in the http request and checks that it is the same as currentStep
 * returns
 * true = sequence exists in the session and in the http request
 * false = anything else
 *
 */
	function checkIsOk($sequence) {
		$httpstep = '';
		$id = $sequence->id;
		$currentStep = $sequence->currentStep;
		if (isset($_POST['currentStep'])) {
			$httpstep = $_POST['currentStep'];
		} else if (isset($_GET['currentStep'])) {
			$httpstep = $_GET['currentStep'];
		} else {
			return false;
		}
		if (!isset($_SESSION['seq']) || !isset($_SESSION['seqStep'])) {
			return false;
		}
		if ($id != $_SESSION['seq'] || $currentStep != $_SESSION['seqStep']) {
			return false;
		} else {
		return (isset($_SESSION['seqStep']) && ($httpstep==$_SESSION['seqStep']));
		}
	}
	
?>