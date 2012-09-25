<?php
if (!isset($gCms)) exit;

#
#The tab headers
#
echo $this->StartTabHeaders();
if (FALSE == empty($params['active_tab']))
  {
    $tab = $params['active_tab'];
  } else {
  $tab = '';
 }
if ($this->CheckPermission('Modify News') || $this->CheckPermission('Approve News') )
  {
    echo $this->SetTabHeader('articles',$this->Lang('articles'), ('articles' == $tab)?true:false);
  }
if( $this->CheckPermission('Modify Site Preferences') )
  {
    echo $this->SetTabHeader('categories',$this->Lang('categories'), ('categories' == $tab)?true:false);
    echo $this->SetTabHeader('customfields',$this->Lang('customfields'),
			     ('customfields'==$tab)?true:false);
  }
					
if ($this->CheckPermission('Modify Templates'))
  {
    echo $this->SetTabHeader('summary_template',$this->Lang('summarytemplate'), ('summary_template' == $tab)?true:false);
    echo $this->SetTabHeader('detail_template',$this->Lang('detailtemplate'), ('detail_template' == $tab)?true:false);
    echo $this->SetTabHeader('form_template',$this->Lang('formtemplate'),
			     ('form_template' == $tab)?true:false);
    echo $this->SetTabHeader('browsecat_template',$this->Lang('browsecattemplate'),
			     ('browsecat_template' == $tab)?true:false);
    echo $this->SetTabHeader('default_templates',
			     $this->Lang('default_templates'),
			     ('default_templates' == $tab)?true:false);
  }
			
if ($this->CheckPermission('Modify Site Preferences'))
  {
    echo $this->SetTabHeader('options',$this->Lang('options'), ('options' == $tab)?true:false);
  }
echo $this->EndTabHeaders();

#
#The content of the tabs
#
echo $this->StartTabContent();
if ($this->CheckPermission('Modify News') || $this->CheckPermission('Approve News') )
  {
    echo $this->StartTab('articles', $params);
    include(dirname(__FILE__).'/function.admin_articlestab.php');
    echo $this->EndTab();
  }
if ($this->CheckPermission('Modify Site Preferences'))
  {
    echo $this->StartTab('categories', $params);
    include(dirname(__FILE__).'/function.admin_categoriestab.php');
    echo $this->EndTab();

    echo $this->StartTab('customfields', $params);
    include(dirname(__FILE__).'/function.admin_customfieldstab.php');
    echo $this->EndTab();
  }
if( $this->CheckPermission( 'Modify Templates' ) )
  {
    echo $this->StartTab('summary_template', $params);
    {
      echo '<h3>'.$this->Lang('title_available_templates').'</h3>';
      news_admin_ops::AdminCreateTemplateList($id,$returnid,
					      'summary',
					      'default_summary_template_contents',
					      'summary_template',
					      'current_summary_template',
					      $this->Lang('title_summary_template'));
    }
    echo $this->EndTab();
	
    echo $this->StartTab('detail_template', $params);
    {
      echo '<h3>'.$this->Lang('title_available_templates').'</h3>';
      news_admin_ops::AdminCreateTemplateList($id,$returnid,
					      'detail',
					      'default_detail_template_contents',
					      'detail_template',
					      'current_detail_template',
					      $this->Lang('title_detail_template'));
    }
    echo $this->EndTab();

    echo $this->StartTab('form_template', $params);
    {
      echo '<h3>'.$this->Lang('title_available_templates').'</h3>';
      news_admin_ops::AdminCreateTemplateList($id,$returnid,
					      'form',
					      'default_form_template_contents',
					      'form_template',
					      'current_form_template',
					      $this->Lang('title_form_template'));
    }
    echo $this->EndTab();

    echo $this->StartTab('browsecat_template', $params);
    {
      echo '<h3>'.$this->Lang('title_available_templates').'</h3>';
      news_admin_ops::AdminCreateTemplateList($id,$returnid,
					      'browsecat',
					      'default_browsecat_template_contents',
					      'browsecat_template',
					      'current_browsecat_template',
					      $this->Lang('title_browsecat_template'));
    }
    echo $this->EndTab();

    echo $this->StartTab('default_templates');
    {
      echo '<p>'.$this->Lang('info_sysdefault2').'</p>';

      news_admin_ops::AdminEditDefaultTemplateForm($id,$returnid,
					   'default_summary_template_contents',
					   'default_templates',
					   $this->Lang('title_summary_sysdefault'),
					   'orig_summary_template.tpl',
					   $this->Lang('info_sysdefault'));

      echo '<hr/>';

      news_admin_ops::AdminEditDefaultTemplateForm($id,$returnid,
					   'default_detail_template_contents',
					   'default_templates',
					   $this->Lang('title_detail_sysdefault'),
					   'orig_detail_template.tpl',
					   $this->Lang('info_sysdefault'));

      echo '<hr/>';


      news_admin_ops::AdminEditDefaultTemplateForm($id,$returnid,
					   'default_form_template_contents',
					   'default_templates',
					   $this->Lang('title_form_sysdefault'),
					   'orig_form_template.tpl',
					   $this->Lang('info_sysdefault'));

      echo '<hr/>';


      news_admin_ops::AdminEditDefaultTemplateForm($id,$returnid,
					   'default_browsecat_template_contents',
					   'default_templates',
					   $this->Lang('title_browsecat_sysdefault'),
					   'browsecat.tpl',
					   $this->Lang('info_sysdefault'));
    }
    echo $this->EndTab();

  }

if ($this->CheckPermission('Modify Site Preferences'))
  {
    echo $this->StartTab('options', $params);
    include(dirname(__FILE__).'/function.admin_optionstab.php');
    echo $this->EndTab();
  }

echo $this->EndTabContent();

# vim:ts=4 sw=4 noet
?>
