<?php
/*
 * sequence - a helper class for module developers
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
#
#$Id: class.template.inc.php 1090 2004-12-28 18:12:54Z wishy $

class Sequence
{
	var $id;
	var $name;
	var $actions;
	var $panic;
	var $active;
	var $currentStep;

	function Sequence()
	{
		$this->SetInitialValues();
	}

	function SetInitialValues()
	{
		$this->id = -1;
		$this->name = '';
		$this->actions = '';
		$this->panic = '';
		$this->active = false;
		$this->currentStep = -1;
	}

	function Save()
	{
		$result = false;
		
		if ($this->id > -1)
		{
			$result = SequenceOperations::UpdateSequence($this);
		}
		else
		{
			$newid = SequenceOperations::InsertSequence($this);
			if ($newid > -1)
			{
				$this->id = $newid;
				$result = true;
			}

		}

		return $result;
	}

	function Delete()
	{
		$result = false;

		if ($this->id > -1)
		{
			$result = SequenceOperations::DeleteSequenceByID($this->id);
			if ($result)
			{
				$this->SetInitialValues();
			}
		}

		return $result;
	}
}

class SequenceOperations
{
	function LoadSequences()
	{
		global $gCms;
		$db = &$gCms->db;

		$result = array();

		$query = "SELECT sequence_id, sequence_name, sequence_actions, sequence_panic, active FROM ".cms_db_prefix()."sequences ORDER BY sequence_name";
		$dbresult = $db->Execute($query);

		if ($dbresult && $dbresult->RowCount() > 0)
		{
			while ($row = $dbresult->FetchRow())
			{
				$onesequence = new Sequence();
				$onesequence->id = $row['sequence_id'];
				$onesequence->name = $row['sequence_name'];
				$onesequence->active = $row['active'];
				$onesequence->actions = $row['sequence_actions'];
				$onesequence->panic = $row['sequence_panic'];
				$onesequence->currentStep = -1;
				array_push($result, $onesequence);
			}
		}

		return $result;
	}

	function LoadSequenceByID($id)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->db;
		
		if ( is_numeric($id) ) {

			$query = "SELECT sequence_id, sequence_name, sequence_actions, sequence_panic, active FROM ".cms_db_prefix()."sequences WHERE sequence_id = ?";
			
		} else {
			
			$query = "SELECT sequence_id, sequence_name, sequence_actions, sequence_panic, active FROM ".cms_db_prefix()."sequences WHERE sequence_name = ?";
			
		}
		$dbresult = $db->Execute($query, array($id));

		if ($dbresult && $dbresult->RowCount() > 0)
		{
			while ($row = $dbresult->FetchRow())
			{
				$onesequence = new Sequence();
				$onesequence->id = $row['sequence_id'];
				$onesequence->name = $row['sequence_name'];
				$onesequence->actions = $row['sequence_actions'];
				$onesequence->panic = $row['sequence_panic'];
				$onesequence->active = $row['active'];
				$onesequence->currentStep = 0;
				$_SESSION['seq'] = $onesequence->id;
				$_SESSION['seqStep'] = $onesequence->currentStep;
				$result = $onesequence;
			}
		}	
		return $result;
	}

	function InsertSequence($sequence)
	{
		$result = -1; 

		global $gCms;
		$db = &$gCms->db;

		$new_sequence_id = $db->GenID(cms_db_prefix()."sequences_seq");
		$query = "INSERT INTO ".cms_db_prefix()."sequences (sequence_id, sequence_name, sequence_actions, sequence_panic, active, create_date, modified_date) VALUES (?,?,?,?,?,?)";
		$dbresult = $db->Execute($query, array($new_sequence_id, $sequence->name, $sequence->actions, $sequence->panic, $sequence->active, $db->DBTimeStamp(time()), $db->DBTimeStamp(time())));
		if ($dbresult !== false)
		{
			$result = $new_sequence_id;
		}

		return $result;
	}

	function UpdateSequence($sequence)
	{
		$result = false; 

		global $gCms;
		$db = &$gCms->db;

		$query = "UPDATE ".cms_db_prefix()."sequences SET sequence_name = ?, sequence_actions = ?, sequence_panic = ?, active = ?, modified_date = ? WHERE template_id = ?";
		$dbresult = $db->Execute($query,array($sequence->name,$sequence->actions,$sequence->panic,$sequence->active,$db->DBTimeStamp(time()),$sequence->id));
		if ($dbresult !== false)
		{
			$result = true;
		}

		return $result;
	}

	function DeleteSequenceByID($id)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->db;

		$query = "DELETE FROM ".cms_db_prefix()."sequences where sequence_id = ?";
		$dbresult = $db->Execute($query,array($id));

		if ($dbresult !== false)
		{
			$result = true;
		}

		return $result;
	}

	function CountPagesUsingSequenceByID($id)
	{
		$result = 0;

		global $gCms;
		$db = &$gCms->db;

        $query = "SELECT count(*) AS count FROM ".cms_db_prefix()."pages WHERE sequence_id = ?";
        $dbresult = $db->Execute($query,array($id));

		if ($dbresult && $dbresult->RowCount() > 0)
		{
			$row = $dbresult->FetchRow();
			if (isset($row["count"]))
			{
				$result = $row["count"];
			}
		}

		return $result;
	}

	function TouchAllSequences($seq_name='')
	{
		$result = false;

		global $gCms;
		$db = &$gCms->db;

		$dbresult = false;

		if ($seq_name != '')
		{
			$query = "UPDATE ".cms_db_prefix()."sequences SET modified_date = ? WHERE sequence_content like ?";
			$dbresult = $db->Execute($query,array($db->DBTimeStamp(time()),'%{html_seq name="'.$seq_name.'"}%'));
		}
		else
		{
			$query = "UPDATE ".cms_db_prefix()."sequences SET modified_date = ?";
			$dbresult = $db->Execute($query,array($db->DBTimeStamp(time())));
		}

		if ($dbresult !== false)
		{
			$result = true;
		}

		return $result;
	}

	function CheckExistingSequenceName($name)
	{
		$result = false;

		global $gCms;
		$db = &$gCms->db;

		$query = "SELECT sequence_id from ".cms_db_prefix()."sequences WHERE sequence_name = ?";
		$dbresult = $db->Execute($query,array($name));

		if ($dbresult && $dbresult->RowCount() > 0)
		{
			$result = true; 
		}

		return $result;
	}
	
	function SequenceDropdown($id = 'sequence_id', $selected_id = -1, $othertext = '')
	{
		$result = "";

		$allsequences = SequenceOperations::LoadSequences();
		
		if (count($allsequences) > 0)
		{
			$result .= '<select name="'.$id.'"';
			if ($othertext != '')
			{
				$result .= ' ' . $othertext;
			}
			$result .= '>';
			$result .= '<option value="">Select Sequence</option>';
			foreach ($allsequences as $onesequence)
			{
				$result .= '<option value="'.$onesequence->id.'"';
				if ($onesequence->id == $selected_id)
				{
				    $result .= ' selected="selected"';
				}
				$result .= '>'.$onesequence->name.'</option>';
			}
			$result .= '</select>';
		}
		
		return $result;
	}
}

# vim:ts=4 sw=4 noet
?>
