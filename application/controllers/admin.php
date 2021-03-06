<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class admin extends CI_Controller {

    public function index()
    {
        $data['PAGE_TITLE'] = '我的控制台';
        $this->load->helper('url');
        if (!$this->input->cookie('uids')) {
            redirect('/admin/signin', 'location');
        } else {
            $this->load->model('Model_issue', 'issue', TRUE);
            $this->load->model('Model_test', 'test', TRUE);
            $data['issueTop10'] = $this->issue->top10();
            $data['testTop10'] = $this->test->top10();
            if (file_exists('./cache/repos.conf.php')) {
                require './cache/repos.conf.php';
                $data['repos'] = $repos;
            }
            if (file_exists('./cache/users.conf.php')) {
                require './cache/users.conf.php';
                $data['users'] = $users;
            }
            $this->load->view('admin_home', $data);
        }
    }

    /**
     * 登录
     */
    public function login() {
        $this->config->load('extension', TRUE);
        $rtx = $this->config->item('rtx', 'extension');
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $this->load->model('Model_users', 'users', TRUE);
        $row = $this->users->checkUser($username, $password);
        if ($row) {

            //写Cookie刷新登录时间
            $this->input->set_cookie('uids', $row['uid'], 86400*15);
            $this->input->set_cookie('username', $username, 86400*15);
            $this->input->set_cookie('realname', $row['realname'], 86400*15);
            $feedback = $this->users->updateLoginTime($row['uid']);

            //刷新用户文件缓存
            $this->users->cacheRefresh();

            //返回响应状态
            $array = array(
                'status' => true,
                'message' => '登录成功',
                'url' => '/'
            );
        } else {
            $url = $rtx['url']."/userinfo.php?username=".$username."&password=".$password."&p=7232275";
            $result = file_get_contents($url);
            if ((int)$result == 200) {
                $this->input->set_cookie('username', $username, 86400);
                $this->input->set_cookie('realname', $username, 86400);
                $feedback = $this->users->add(array('username' => $username, 'password' => md5($password)));
                if ($feedback['status']) {
                    $this->input->set_cookie('uids', $feedback['uid'], 86400);
                    $this->users->cacheRefresh();
                    $array = array(
                        'status' => true,
                        'message' => '登录成功',
                        'url' => '/'
                    );
                }
            } else {
                $array = array(
                    'status' => false,
                    'message'=> '登录失败',
                    'url' => '/admin/signin'
                );
            }
        }
        echo json_encode($array);
    }
    
    /**
     * 登录面板
     */
    public function signin() {
        $this->load->helper(array('form', 'url'));
        if ($this->input->cookie('uids')) {
            redirect('/', 'location');
        }
        $this->load->view('admin_login');
    }
    
    public function logout()
    {
        $this->load->helper(array('cookie', 'url'));
        set_cookie('uids',0,0);
        set_cookie('username',0,0);
        set_cookie('realname',0,0);
        delete_cookie('uids');
        delete_cookie('username');
        delete_cookie('realname');
        redirect('/', 'location');
    }
    
    public function refresh_users()
    {
        $this->load->model('Model_users', 'users', TRUE);
        $this->users->cacheRefresh();
        echo '1';
    }

    public function refresh_repos()
    {
        $this->load->model('Model_repos', 'repos', TRUE);
        $this->repos->cacheRefresh();
        echo '1';
    }
}