<?php

require_once('class.CmsApp.pseudo.php');
require_once(CMSMS.'/lib/classes/class.cms_siteprefs.php');

class Test_cms_siteprefs extends UnitTestCase
{
  public function setUp()
  {
    parent::setUp();

    $config = cmsms()->GetConfig();
    $dbdict = NewDataDictionary(cmsms()->GetDb());
    $taboptarray = array('mysql' => 'ENGINE MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci', 
			 'mysqli' => 'ENGINE MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci');

    $flds = "
	sitepref_name C(255) KEY,
	sitepref_value text,
	create_date DT,
	modified_date DT
    ";
    $sqlarray = $dbdict->CreateTableSQL($config['db_prefix'].'siteprefs', $flds, $taboptarray);
    $return = $dbdict->ExecuteSQLArray($sqlarray);
  }

  public function tearDown()
  {
    $config = cmsms()->GetConfig();
    $dbdict = NewDataDictionary(cmsms()->GetDb());
    $sqlarray = $dbdict->DropTableSQL($config['db_prefix'].'siteprefs');
    $return = $dbdict->ExecuteSQLArray($sqlarray);
  }

  public function TestSetGet1()
  {
    cms_siteprefs::set('test1','val1');
    cms_siteprefs::set('test2','val2');
    $this->assertEqual(cms_siteprefs::get('test1'),'val1');
  }

  public function TestExists()
  {
    $this->assertTrue(cms_siteprefs::exists('test1'));
  }

  public function TestRemove()
  {
    cms_siteprefs::remove('test2');
    $this->assertFalse(cms_siteprefs::exists('test2'));
  }

  public function TestExists2()
  {
    cms_siteprefs::set('test1','');
    $this->assertTrue(cms_siteprefs::exists('test1'));

    cms_siteprefs::set('test1',null);
    $this->assertTrue(cms_siteprefs::exists('test1'));
  }
} // end of class
?>