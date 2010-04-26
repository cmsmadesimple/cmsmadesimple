<?php
if (!isset($gCms)) die("Can't call actions directly!");

//Set for the new blog posting
$blog_post = new BlogPost();
$blog_post->author_id = CmsLogin::get_current_user()->id;

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
		
		if (isset($params['submitpublish']))
		{
			CmsAdminTheme::get_instance()->add_message($this->lang('post_created'));
		}
		else
		{
			CmsAdminTheme::get_instance()->add_message($this->lang('post_published'));
		}

		$blog_post = new BlogPost();
		$blog_post->author_id = CmsLogin::get_current_user()->id;
	}
}

$smarty->assign('selected_tab', coalesce_key($params, 'selected_tab', 'writepost'));
$smarty->assign('form_action', 'defaultadmin');
$smarty->assign('blog_post', $blog_post);
$smarty->assign('post_date_prefix', $id . 'post_date_');

$smarty->assign('posts', cms_orm('BlogPost')->find_all(array('order' => 'id desc')));
$smarty->assign('categories', cms_orm('BlogCategory')->find_all(array('order' => 'name ASC')));
//$smarty->assign('processors', CmsTextProcessor::list_processors_for_dropdown());

$smarty->assign('writepost', $this->Template->process('editpost.tpl', $id, $return_id));
$smarty->assign('manageposts', $this->Template->process('listposts.tpl', $id, $return_id));
echo $this->Template->process('defaultadmin.tpl', $id, $return_id);

# vim:ts=4 sw=4 noet
?>