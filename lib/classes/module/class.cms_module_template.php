<?php // -*- mode:php; tab-width:4; indent-tabs-mode:t; c-basic-offset:4; -*-
#CMS - CMS Made Simple
#(c)2004-2008 by Ted Kulp (ted@cmsmadesimple.org)
#This project's homepage is: http://cmsmadesimple.org
#
#This program is free software; you can redistribute it and/or modify
#it under the terms of the GNU General Public License as published by
#the Free Software Foundation; either version 2 of the License, or
#(at your option) any later version.
#
#This program is distributed in the hope that it will be useful,
#but WITHOUT ANY WARRANTY; without even the implied warranty of
#MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#GNU General Public License for more details.
#You should have received a copy of the GNU General Public License
#along with this program; if not, write to the Free Software
#Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
#
#$Id$

class CmsModuleTemplate extends CmsObjectRelationalMapping
{
    var $params = array('id' => -1, 'name' => '', 'module' => '', 'template_type' => '', 'default' => false);
    var $field_maps = array('template_name' => 'name', 'module_name' => 'module', 'default_template' => 'default');
    var $table = 'module_templates';

    function __construct()
    {
        parent::__construct();
    }
    
    function validate()
    {
        $this->validate_not_blank('name', lang('nofieldgiven', array(lang('name'))));
        $this->validate_not_blank('content', lang('nofieldgiven', array(lang('content'))));
        if ($this->name != '')
        {
            $result = $this->find_count(array('conditions' => array('template_name = ? and id <> ? and template_type = ? and module_name = ?', $this->name, $this->id, $this->template_type, $this->module)));
            if ($result > 0)
            {
                $this->add_validation_error(lang('templateexists'));
            }
        }
    }
    
    function before_save()
    {
        //Handle some logic for the default template
        $count = $this->find_count(array('conditions' => array('template_type = ? and module_name = ?', $this->template_type, $this->module)));
        if ($count == 0 && $this->default == false)
        {
            $this->default = true;
        }
    }
    
    function after_save()
    {
        //Reset all other templates to have a false default if this one is true
        if ($this->default)
        {
            $cms_db_prefix = CMS_DB_PREFIX;
            cms_db()->Execute("UPDATE {$cms_db_prefix}module_templates SET default_template = 0 WHERE template_type = ? AND module_name = ? AND id <> ?", array($this->template_type, $this->module, $this->id));
        }
    }
    
    /**
     * Get a list of all the modules that have templates installed.
     *
     * @return array The list of modules with templates
     * @author Ted Kulp
     **/
    public static function get_modules()
    {
        $result = array();
        $cms_db_prefix = CMS_DB_PREFIX;
        
        $rows = cms_db()->GetAll("SELECT distinct module_name as module FROM {$cms_db_prefix}module_templates order by module_name");
        if ($rows)
        {
            foreach ($rows as $one_row)
            {
                $result[] = $one_row['module'];
            }
        }
        
        return $result;
    }
    
    /**
     * Get a list of all the template types for the given module name.  If no
     * module name is given, then it will return a nested array with module name
     * and a list of templates as their children.
     *
     * @return array The list of templates and optionally, modules
     * @author Ted Kulp
     **/
    public static function get_template_types($module_name = '')
    {
        $result = array();
        $cms_db_prefix = CMS_DB_PREFIX;
        
        if ($module_name != '')
        {
            $rows = cms_db()->GetAll("SELECT distinct template_type as type FROM {$cms_db_prefix}module_templates WHERE module_name = ? order by module_name", array($module_name));
            if ($rows)
            {
                foreach ($rows as $one_row)
                {
                    if ($one_row['type'] != null && $one_row['type'] != '')
                        $result[] = $one_row['type'];
                }
            }
        }
        else
        {
            $rows = cms_db()->GetAll("SELECT template_type as type, module_name as module FROM {$cms_db_prefix}module_templates group by template_type order by module_name");
            if ($rows)
            {
                foreach ($rows as $one_row)
                {
                    if ($one_row['type'] != null && $one_row['type'] != '')
                        $result[$one_row['module']][] = $one_row['type'];
                }
            }
        }
        
        return $result;
    }
    
    /**
     * Return a list of all the template objects, nested inside template types, which are in turn
     * nested inside module names.  This allows for easy display by using nested foreach statements
     * for display.  If you just want the objects themselves, then the regular orm is the way to do.
     *
     * @return array The templates, template types and modules
     * @author Ted Kulp
     **/
    public static function get_all_templates()
    {
        $result = array();

        $templates = cms_orm('CmsModuleTemplate')->find_all(array('conditions' => array("template_type <> ?", ''), 'order' => 'module ASC, template_type ASC'));
        foreach ($templates as $one_template)
        {
            $result[$one_template->module][$one_template->template_type][] = $one_template;
        }
        
        return $result;
    }
}

# vim:ts=4 sw=4 noet
?> 
