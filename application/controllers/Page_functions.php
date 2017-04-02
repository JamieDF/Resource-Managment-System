<?php
class Page_functions extends CI_Controller {

	public function __construct()
			{
					parent::__construct();
					$this->load->model('profile_model');
					$this->load->model('project_model');
					$this->load->helper('url_helper');
					$this->load->library('session');
					$this->load->helper('url');

				
			}

	public function check_restricted() {
			$this->db->reconnect();
			$url = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
			if( empty($this->session->userdata('logged_in'))){	
				if(strpos($url,'/login') == false) {
					$data['title'] = 'Restricted Page, Please log in';
					$this->load->view('templates/header', $data);
					$this->load->view('templates/footer');
					redirect('login', 'refresh');
					//$this->output->set_header('refresh:3; url='.site_url('login'));
				}
				return false;					
			}
			return true;
	}


	
	public function index(){
		redirect('login', 'refresh');
	}
	
		
	public function create()
	{
		$this->load->helper('form');
		$this->load->library('form_validation');
		//~ $this->load->library('encryption');
		
		$data['title'] = 'Register Profile';

		$this->form_validation->set_rules('username', 'username', 'required');
		$this->form_validation->set_rules('password', 'password', 'required');
		$this->form_validation->set_rules('emailAddress', 'emailAddress', 'required');
		//~ $password = 'mypassword';
		//~ $hash = crypt($password);

		if ($this->form_validation->run() === FALSE)
		{
			$this->load->view('templates/header', $data);
			$this->load->view('pages/create');
			$this->load->view('templates/footer');

		}
		else
		{
			$data['created'] = "Your account has been successfully registerred, please log in to set up your profile.";
			$this->profile_model->set_account();
			$this->load->view('templates/header', $data);
			$this->load->view('pages/login',$data);
			$this->load->view('templates/footer');
		}
	}
	public function profile(){
		if($this->check_restricted() == false) {return;};
		$this->load->helper('form');
		$this->load->library('form_validation');
		
		$data['title'] = 'Edit profile';
		
		$this->form_validation->set_rules('fname', 'fname', 'required');
		$this->form_validation->set_rules('sname', 'sname', 'required');
		$this->form_validation->set_rules('dob', 'dob', 'required');
		
		$this->form_validation->set_rules('religion', 'religion');
		$this->form_validation->set_rules('LocationFlex', 'LocationFlex');
		
		
		$this->form_validation->set_rules('country', 'country', 'required');
		$this->form_validation->set_rules('city', 'city', 'required');
		$this->form_validation->set_rules('postcode', 'postcode', 'required');
		$this->form_validation->set_rules('streetName', 'streetName', 'required');
		$this->form_validation->set_rules('buildingNumber', 'buildingNumber', 'required');

		$data['info'] =  ($this->profile_model->load_profile());
		if ($this->form_validation->run() === FALSE)
		{
			$this->load->view('templates/profile_header', $data);
			$this->load->view('pages/profile_edit');
			$this->load->view('templates/footer');

		}
		else
		{
			$this->profile_model->set_profile();
			redirect('view_profile', $data);

		} 
}

public function find_profile($usrname){	
			
			$this->check_restricted();
			$this->load->helper('form');
			$this->load->library('form_validation');
			
			$data['info'] = $this->profile_model->join_find_profile($usrname);
			$data['history'] = $this->project_model->load_project_history($usrname);
			$data['time_off'] = $this->profile_model->availability($usrname);
			$data['find'] = true;
			
			$this->load->view('templates/profile_header', $data);
			$this->load->view('pages/show_profile');
			$this->load->view('templates/footer');
		
		}
		
public function view_profile(){	
			
			$this->check_restricted();
			$this->load->helper('form');
			$this->load->library('form_validation');
			
			$data['info'] = $this->profile_model->join_load_profile(NULL);
			$data['history'] = $this->project_model->load_project_history(NULL);
			$data['time_off'] = $this->profile_model->availability(NULL);
			$this->form_validation->set_rules('startDate', 'start date ', 'required');
			$this->form_validation->set_rules('endDate', 'end date ', 'required');
			
					if ($this->form_validation->run() === FALSE)
		{ 	
			$this->load->view('templates/profile_header', $data);
			$this->load->view('pages/show_profile');
			$this->load->view('templates/footer');
		} else
			{
				$data['leave'] = $this->profile_model->day_off();
				
			$this->load->view('templates/profile_header', $data);
			$this->load->view('pages/show_profile');
			$this->load->view('templates/footer');
			}
		
		}
		
public function adminreg(){	
			
			$this->check_restricted();
			$this->load->helper('form');
			$this->load->library('form_validation');
			
			$this->load->view('templates/profile_header', $data);
			$this->load->view('pages/admin');
			$this->load->view('templates/footer');
		
		}
	
	
public function search(){
	
		$this->check_restricted();
		$this->load->helper('form');
		$this->load->library('form_validation');
	
		
		$this->form_validation->set_rules('search', 'Search', 'required');
		if ($this->form_validation->run() === FALSE){
			$this->load->view('templates/header');
			$this->load->view('pages/profile_search');
			$this->load->view('templates/footer');
		}
		else 
		{
			$search = $this->input->post('search');
			$option = $this->input->post('fields');
			$data['query'] = $this->profile_model->profile_search($option, $search);	
			
			$this->load->view('templates/header');
			$this->load->view('pages/profile_search', $data);
			$this->load->view('templates/footer');
		}
	}

public function view_projects()
{
			$this->check_restricted();
			$this->load->helper('form');
			$this->load->library('form_validation');
			
			
			$data['info'] = $this->project_model->get_all_projects();
			
			$this->load->view('templates/profile_header', $data);
			$this->load->view('pages/project/projects_temp');
			$this->load->view('templates/footer');

}

public function view_profiles()
{
			$this->check_restricted();
			$this->load->helper('form');
			$this->load->library('form_validation');
			
			
			$data['info'] = $this->profile_model->get_all_profiles();
			
			$this->load->view('templates/profile_header', $data);
			$this->load->view('pages/profile_db');
			$this->load->view('templates/footer');

}

public function view_my_project()
{
			$this->check_restricted();
			$this->load->helper('form');
			$this->load->library('form_validation');
			
			
			$data['info'] = $this->project_model->get_my_projects(); // you need to get the personID from account ID 
			
			$this->load->view('templates/profile_header', $data);
			$this->load->view('pages/project/my_project');
			$this->load->view('templates/footer');

}


public function find_project($projectID){	
			
			$this->check_restricted();
			$this->load->helper('form');
			$this->load->library('form_validation');
			
			$data['info'] = $this->project_model->join_find_project($projectID);
			$data['tasks'] = $this->project_model->find_tasks($projectID);
			$data['roles'] = $this->project_model->find_assignment_roles($projectID);
			$data['rolesback'] = $this->project_model->find_roles($projectID);

			$data['skills'] = $this->project_model->find_role_skills($projectID);
			
			$this->load->view('templates/profile_header', $data);
			$this->load->view('pages/project/show_project');
			$this->load->view('templates/footer');
		
		}
/*
public function view_project($projectID){	
			
			$this->check_restricted();
			$this->load->helper('form');
			$this->load->library('form_validation');
			
			$data['info'] = $this->project_model->join_load_project($projectID);
			$data['tasks'] = $this->project_model->find_tasks($projectID);
			$data['roles'] = $this->project_model->find_roles($projectID);
			
			
			$this->load->view('templates/profile_header');
			$this->load->view('pages/project/show_project');
			$this->load->view('templates/footer');
		
		}
*/
public function edit_project($projectID){
		if($this->check_restricted() == false) {return;};
		$this->load->helper('form');
		$this->load->library('form_validation');
		

		$data['title'] = 'Edit project';
		$data['project'] = $projectID;
		
		$this->form_validation->set_rules('projectTitle', 'projectTitle', 'required');
		$this->form_validation->set_rules('projectType', 'projectType', 'required');
		$this->form_validation->set_rules('startDate', 'startDate', 'required');
		$this->form_validation->set_rules('endDate', 'endDate', 'required');
		$this->form_validation->set_rules('projectBudget', 'projectBudject', 'required');

		$this->form_validation->set_rules('country', 'country', 'required');
		$this->form_validation->set_rules('city', 'city', 'required');
		$this->form_validation->set_rules('postcode', 'postcode', 'required');
		$this->form_validation->set_rules('streetName', 'streetName', 'required');
		$this->form_validation->set_rules('buildingNumber', 'buildingNumber', 'required');


		$data['info'] =  ($this->project_model->join_find_project($projectID));
		$data['tasks'] = $this->project_model->find_tasks($projectID);
		$data['roles'] = $this->project_model->find_roles($projectID);
		if ($this->form_validation->run() === FALSE)
		{
			$this->load->view('templates/profile_header', $data);
			$this->load->view('pages/project/project_edit');
			$this->load->view('templates/footer');

		}
		else
		{
			$this->project_model->edit_tasks($projectID);
			$this->project_model->edit_project($projectID);
			redirect('find_project/'.$projectID);

		} 
}

public function edit_tasks($projectID){
		if($this->check_restricted() == false) {return;};
		$this->load->helper('form');
		$this->load->library('form_validation');

		$task = $this->input->post('task');
		print_r ($task);
		 //$roles[] = array();
		if(!empty($task))
		{
			echo "<br>".sizeof($task);
			// Loop through hotels and add the validation
			foreach($task as $id => $data)
			{
				$this->form_validation->set_rules('task[' . $id . '][title]', 'required');
				$this->form_validation->set_rules('task[' . $id . '][startDate]','required');
				$this->form_validation->set_rules('task[' . $id . '][endDate]', 'required');
				$role = $this->input->post('task[' . $id . '][role]');
				//unset($roles);
				//$roles[] = array();
				foreach($role as $id2 => $role)
				{
					$this->form_validation->set_rules('task[' . $id . '][role]['.$id2.'][name]', 'Task '.$id.' Role name '. $id2 , 'required');
					$this->form_validation->set_rules('task[' . $id . '][role]['.$id2.'][numPeople]', 'Task '.$id.' Number of People '. $id2 , 'required');
					$this->form_validation->set_rules('task[' . $id . '][role]['.$id2.'][skill][0][skillID]', 'Task '.$id.' A Skill for Role '. $id2 , 'required');


				}
			}
		}
	
			
		

		if ($this->form_validation->run() === FALSE)
		{
			$data['title'] = 'Edit Tasks';
	
			$data['info'] =  ($this->project_model->join_find_project($projectID));
			$data['tasks'] = $this->project_model->find_tasks($projectID);
			$data['roles'] = $this->project_model->find_roles($projectID);
			$data['skills'] = $this->project_model->find_role_skills($projectID);
			$data['projectID'] = $projectID;

			$this->load->view('templates/profile_header', $data);
			$this->load->view('pages/project/edit_tasks', $data);
			$this->load->view('templates/footer');

		}
		else
		{
			$this->project_model->edit_tasks($projectID);
			redirect('find_project/'.$projectID);
		} 
    }


public function search_project(){
	
		$this->check_restricted();
		$this->load->helper('form');
		$this->load->library('form_validation');
	
		
		$this->form_validation->set_rules('search', 'Search', 'required');
		if ($this->form_validation->run() === FALSE){
			$this->load->view('templates/header');
			$this->load->view('pages/project/project_search');
			$this->load->view('templates/footer');
		}
		else 
		{
			$search = $this->input->post('search');
			$option = $this->input->post('fields');
			$data['query'] = $this->project_model->project_search($option, $search);	
			
			$this->load->view('templates/header');
			$this->load->view('pages/project/project_search', $data);
			$this->load->view('templates/footer');
		}
	}

public function interest_project($projectID ){	
			$this->check_restricted();
			$this->load->helper('form');
			$this->load->library('form_validation');
				
			$data['info'] = $this->project_model->join_find_project($projectID);
			$data['interest'] = $this->project_model->find_interest_project($projectID);
			$data['project'] = $projectID;
			
			$this->form_validation->set_rules('sub', 'submit', 'required');

			//$data['find'] = true;
			if ($this->form_validation->run() === FALSE){
				
				$this->load->view('templates/profile_header', $data);
				$this->load->view('pages/project/interest_project');
				$this->load->view('templates/footer');
			} 
			else
			{
				$data['interest'] = $this->project_model->add_interest_project($projectID);
				$data['interest'] = $this->project_model->find_interest_project($projectID);
				$this->load->view('templates/profile_header', $data);
				$this->load->view('pages/project/interest_project');
				$this->load->view('templates/footer');
			}
	}
	
public function admin_fill_skills($username){
	if($this->check_restricted() == false) {return;};
	$this->load->helper('form');
	$this->load->library('form_validation');

	$data['info'] = $this->profile_model->join_profile_skills($username);
	$data['skills'] =  $this->project_model->load_skills();
	$data['acc'] = $this->profile_model->getAccountID($username);
	print_r($this->profile_model->getAccountID($username));
    $data['username'] = $username;

	if(isset($skills))
    $this->form_validation->set_rules('skill[][]', 'Skill acc ID ' , 'required');
    $this->form_validation->set_rules('skill[][]', 'Skill number ' , 'required');
    $this->form_validation->set_rules('skill[][]', 'Skill level  ' , 'required');
    $this->form_validation->set_rules('skill[][]', 'Years of experience ' , 'required');
	
	if ($this->form_validation->run() === FALSE){
		
		$data['title'] = 'Adding Skills to Profile';
		
		$this->load->view('templates/profile_header', $data);
		$this->load->view('pages/add_skills');
		$this->load->view('templates/footer');
	}else{
		$this->profile_model->add_profile_skills();	
        $data['info'] = $this->profile_model->join_profile_skills($username);
		$this->load->view('templates/profile_header', $data);
		$this->load->view('pages/add_skills');
		$this->load->view('templates/footer');	
	}
	
}

public function role_select($projectID)
{
			$this->check_restricted();
			$this->load->helper('form');
			$this->load->library('form_validation');
			
			$data['title'] = 'Select Roles';
			$data['info'] = $this->project_model->join_find_project($projectID);
			$data['tasks'] = $this->project_model->find_tasks($projectID);
			$data['roles'] = $this->project_model->find_roles($projectID);
			$data['match'] = $this->project_model->search_algorithm();
			//$data['find'] = true;
			
			$this->load->view('templates/profile_header', $data);
			$this->load->view('pages/project/role_select');
			$this->load->view('templates/footer');

}


}
