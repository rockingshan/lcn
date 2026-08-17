<?php
namespace App\Controllers;

use App\Access;
use App\LogHelper;

class UserController
{
    public function __construct(private $db) {}
    private function admin(): void { if (empty($_SESSION['is_admin'])) { http_response_code(403); exit('Admin access required.'); } }
    public function index(): void {
        $this->admin(); $users = $this->db->query('SELECT user_id,user,first_name,last_name,email,is_admin,is_active FROM auth_tb ORDER BY user')->fetch_all(MYSQLI_ASSOC);
        require __DIR__ . '/../../views/users.php';
    }
    public function form($id = 0): void {
        $this->admin(); $id=(int)$id; $user=null; $selected=[];
        if ($id) { $stmt=$this->db->prepare('SELECT user_id,user,first_name,last_name,email,is_admin,is_active FROM auth_tb WHERE user_id=?'); $stmt->bind_param('i',$id); $stmt->execute(); $user=$stmt->get_result()->fetch_assoc(); $stmt->close();
            $stmt=$this->db->prepare('SELECT permission_key FROM user_permission_tb WHERE user_id=?'); $stmt->bind_param('i',$id); $stmt->execute(); $selected=array_column($stmt->get_result()->fetch_all(MYSQLI_ASSOC),'permission_key'); $stmt->close(); }
        $permissions=Access::PERMISSIONS; require __DIR__ . '/../../views/user_form.php';
    }
    public function save($id = 0): void {
        $this->admin(); $id=(int)$id; $user=trim($_POST['user']??''); $first=trim($_POST['first_name']??''); $email=trim($_POST['email']??''); $password=$_POST['password']??''; $admin=!empty($_POST['is_admin'])?1:0; $active=!empty($_POST['is_active'])?1:0;
        if ($user===''||$first===''||$email===''||(!$id&&$password==='')) exit('Username, first name, email, and password for new users are required.');
        if ($id) { $stmt=$this->db->prepare('UPDATE auth_tb SET user=?,first_name=?,last_name=?,email=?,is_admin=?,is_active=? WHERE user_id=?'); $last=trim($_POST['last_name']??''); $stmt->bind_param('ssssiii',$user,$first,$last,$email,$admin,$active,$id); $stmt->execute(); $stmt->close(); if($password!==''){ $hash=password_hash($password,PASSWORD_DEFAULT); $stmt=$this->db->prepare('UPDATE auth_tb SET pass=? WHERE user_id=?'); $stmt->bind_param('si',$hash,$id); $stmt->execute(); $stmt->close(); }}
        else { $hash=password_hash($password,PASSWORD_DEFAULT); $last=trim($_POST['last_name']??''); $stmt=$this->db->prepare('INSERT INTO auth_tb(user,pass,first_name,last_name,email,is_admin,is_active) VALUES(?,?,?,?,?,?,?)'); $stmt->bind_param('sssssii',$user,$hash,$first,$last,$email,$admin,$active); $stmt->execute(); $id=$this->db->insert_id; $stmt->close(); }
        $stmt=$this->db->prepare('DELETE FROM user_permission_tb WHERE user_id=?'); $stmt->bind_param('i',$id); $stmt->execute(); $stmt->close();
        if (!$admin) { foreach (array_intersect($_POST['permissions']??[],array_keys(Access::PERMISSIONS)) as $key) { $stmt=$this->db->prepare('INSERT INTO user_permission_tb(user_id,permission_key) VALUES(?,?)'); $stmt->bind_param('is',$id,$key); $stmt->execute(); $stmt->close(); } }
        LogHelper::log($this->db,'User Management',"Saved user ID $id"); header('Location: '.BASE_PATH.'/users'); exit();
    }
}
