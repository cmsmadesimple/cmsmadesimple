<?php
if (!isset($gCms)) die("Can't call actions directly!");

if (array_key_exists('cancelpost', $params))
{
	$this->redirect($id, 'defaultadmin', $return_id, array('selected_tab' => 'manageposts'));
}

$blog_post = cms_orm('BlogPost')->find_by_id($params['blog_post_id']);
if ($blog_post == null)
{
	$this->redirect($id, 'defaultadmin', $return_id, array('selected_tab' => 'manageposts'));
}

if (isset($params['submitpost']) || isset($params['submitpublish']))
{
	$blog_post->update_parameters($params['blog_post']);

	if (isset($params['post_date_Month']))
	{
		$blog_post->post_date = new CmsDateTime(mktime($params['post_date_Hour'], $params['post_date_Minute'], $params['post_date_Second'], $params['post_date_Month'], $params['post_date_Day'], $params['post_date_Year']));
	}

	if (isset($params['submitpublish']))
	{
		$blog_post->status = 'publish';
	}

	if ($blog_post->save())
	{
		if (isset($params['blog_post']['category']))
		{
			$blog_post->clear_categories();
			foreach ($params['blog_post']['category'] as $k => $v)
			{
				if ($v == 1)
					$blog_post->set_category($k);
			}
		}

		$this->Redirect->module_url(array('action' => 'defaultadmin', 'selected_tab' => 'manageposts'));
	}
}

$smarty->assign('categories', cms_orm('BlogCategory')->find_all(array('order' => 'name ASC')));
//$smarty->assign('processors', CmsTextProcessor::list_processors_for_dropdown());

$smarty->assign('form_action', 'editpost');
$smarty->assign('post_date_prefix', $id . 'post_date_');
$smarty->assign('blog_post', $blog_post);
echo $this->Template->process('editpost.tpl', $id, $return_id);

# vim:ts=4 sw=4 noet
?>