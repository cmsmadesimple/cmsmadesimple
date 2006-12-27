<?php

echo '<p>Creating ChangeGroupAssign events...';
Events::CreateEvent('Core', 'ChangeGroupAssignPre');
Events::CreateEvent('Core', 'ChangeGroupAssignPost');
echo '[done]</p>';

# vim:ts=4 sw=4 noet
?>
