<?php


echo '<p>Deleting any duplicate events...';
// remove any duplicate core events
$all_events = Events::ListEvents();
if ($all_events !== FALSE)
{
	$core_events = array();
	foreach ($all_events as $event)
	{
		if ($event['originator'] == 'Core')
		{
			if ($id = find_event($core_events, $event['event_name']) != false)
			{
				// move all the handlers
				$q = "UPDATE ".cms_db_prefix()."event_handlers SET event_id = ? WHERE event_id = ?";
				$db->Execute($q, array($id, $event['event_id']));
				// then delete the event
				$q = "DELETE FROM ".cms_db_prefix()."events WHERE  event_id = ?";
				$db->Execute($q, array($event['event_id']));
			}
			else
			{
				$core_events[] = $event;
			}
		}
	}
}
echo '[done]</p>';

echo '<p>Creating ChangeGroupAssign events...';
Events::CreateEvent('Core', 'ChangeGroupAssignPre');
Events::CreateEvent('Core', 'ChangeGroupAssignPost');
echo '[done]</p>';

echo '<p>Updating schema version... ';
$query = 'UPDATE '.cms_db_prefix().'version SET version = 26';
$db->Execute($query);
echo '[done]</p>';

function find_event($arr, $name)
{
	foreach ($arr as $event)
	{
		if ($event['event_name'] == $name)
		{
			return $event['event_id'];
		}
	}
	return false;
}

# vim:ts=4 sw=4 noet
?>
