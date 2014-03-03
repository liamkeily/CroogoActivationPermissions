<?php
App::uses('CakeTime', 'Utility');
App::uses('ClassRegistry', 'Utility');
App::uses('CroogoPlugin', 'Extensions.Lib');
App::uses('DataMigration', 'Extensions.Lib/Utility');
App::uses('File', 'Utility');
App::uses('InstallAppModel', 'Install.Model');
App::uses('Security', 'Utility');

class PluginActivation {
	
  public function onActivation(&$controller) {

	$this->Acl = $controller->Acl;

	// Setup Roles
 	$roles = array(
		'editor'=>'Editor',
		'registered'=>'Registered',
		'author'=>'Author',
	);
	$this->setupRoles($roles);

	// Setup Permissions
	$permissions = array(
		'editor'=>array(
			'allow'=>array(
				'controllers/Nodes/Nodes',	
				'controllers/Nodes/Nodes/admin_index',	
				'controllers/Users/Users',	
				'controllers/Users/Users/admin_index',	
				'controllers/Settings/Settings',	
				'controllers/Settings/Settings/admin_index',	
				'controllers/Menus/Menus',	
				'controllers/Menus/Menus/admin_index',	
				'controllers/Taxonomy/Terms',	
				'controllers/Taxonomy/Terms/admin_index',	
				'controllers/Contacts/Contacts',	
				'controllers/Contacts/Contacts/admin_index',	
				'controllers/Blocks/Blocks',	
				'controllers/Blocks/Blocks/admin_index',	
				'controllers/NodeEvents/NodeEvents',	
				'controllers/NodeImages/NodeImages',	
				'controllers/NodeAttachments/NodeAttachments',	
				'controllers/ElFinder/ElFinder',	
				'controllers/ElFinder/ElFinder/admin_index',	
				'controllers/Meta/Meta',	
			),
			'deny'=>array(

			)
		),
		'author'=>array(
			'allow'=>array(
				'controllers/Nodes',	
				'controllers/NodeEvents',	
				'controllers/NodeImages',	
				'controllers/NodeAttachments',	
				'controllers/ElFinder',	
			),
			'deny'=>array(

			)
		)

	);
	$this->setupPermissions($permissions);
	
  }

  public function beforeActivation(&$controller){
	return true;
  }

  public function beforeDeactivation(&$controller){
	return true;
  }

  public function setupRoles($roles){
	$this->Role = ClassRegistry::init('Role');
	foreach($roles as $roleAlias => $roleTitle){
		$role = $this->Role->find('first',array(
			'conditions'=>array(
				'alias'=>$roleAlias
			)
		));	

		if(!isset($role['Role'])){
			$role['Role'] = array();
		}

		$role['Role']['alias'] = $roleAlias;
		$role['Role']['title'] = $roleTitle;
		$role['Role']['parent_id'] = 2;
		$this->Role->create();
		$this->Role->save($role);
	}
  }

  public function setupPermissions($permissions_array){
	$this->Role = ClassRegistry::init('Role');
	foreach($permissions_array as $roleAlias => $permissions){
		$role = $this->Role->find('first',array(
			'conditions'=>array(
				'alias'=>$roleAlias
			)
		));	
		if(isset($role['Role'])){
			$aclRole =& $this->Role;
			$aclRole->create();
			$aclRole->id = $role['Role']['id'];

			foreach($permissions['allow'] as $allowed){
				$this->Acl->allow($aclRole, $allowed);			
			}

			foreach($permissions['deny'] as $allowed){
				$this->Acl->deny($aclRole, $allowed);			
			}

		}
	}
  }
}
