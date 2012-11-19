<?php

require_once(CMSMS.'/lib/classes/class.cms_tree.php');

class Test_cms_tree extends UnitTestCase
{
  public function TestGetTag()
  {
    $root = new cms_tree('name','root');
    $this->assertEqual($root->get_tag('name'),'root');
  }

  public function TestSetTag()
  {
    $root = new cms_tree('name','root');
    $root->set_tag('name','root2');
    $this->assertEqual($root->get_tag('name'),'root2');
  }

  public function TestAddNode()
  {
    $root = new cms_tree('name','root');
    $child = new cms_tree('name','child');
    $root->add_node($child);
    $this->assertEqual($root->count_nodes(),2);
  }

  public function TestNodeRemove()
  {
    $root = new cms_tree('name','root');
    $child = new cms_tree('name','child');
    $root->add_node($child);
    $child->remove();
    $this->assertEqual($root->count_nodes(),1);
  }

  public function TestNodeRemoveRoot()
  {
    $root = new cms_tree('name','root');
    $child = new cms_tree('name','child');
    $this->assertEqual($root->remove(),FALSE); // this should fail.
  }

  public function TestGetLevel()
  {
    $root = new cms_tree('name','parent');
    $child = new cms_tree('name','child');
    $root->add_node($child);
    $grandchild = new cms_tree('name','grandchild');
    $child->add_node($grandchild);
    $this->assertEqual($grandchild->get_level(),3);
  }

  public function TestCountChildren()
  {
    $root = new cms_tree('name','parent');
    $child1 = new cms_tree('name','child1');
    $child2 = new cms_tree('name','child2');
    $root->add_node($child1);
    $root->add_node($child2);
    $this->assertEqual($root->count_children(),2);
  }

  public function TestCountSiblings()
  {
    $root = new cms_tree('name','parent');
    $child1 = new cms_tree('name','child1');
    $child2 = new cms_tree('name','child2');
    $root->add_node($child1);
    $root->add_node($child2);
    $this->assertEqual($child1->count_siblings(),2);
  }

  public function TestCountSiblingsOfRoot()
  {
    $root = new cms_tree('name','parent');
    $child1 = new cms_tree('name','child1');
    $child2 = new cms_tree('name','child2');
    $root->add_node($child1);
    $root->add_node($child2);
    $this->assertEqual($root->count_siblings(),1);
  }

  public function TestFindByTag()
  {
    $root = new cms_tree('name','parent');
    $child1 = new cms_tree('name','child1');
    $child1_1 = new cms_tree('name','child1.1');
    $child1_2 = new cms_tree('name','child1.2');
    $child2 = new cms_tree('name','child2');
    $child1->add_node($child1_1);
    $child1->add_node($child1_2);
    $root->add_node($child1);
    $root->add_node($child2);

    $this->assertReference($root->find_by_tag('name','child1.2'),$child1_2);
  }

} // end of class

?>