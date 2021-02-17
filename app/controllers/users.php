<?php

include(ROOT_PATH . "/app/database/db.php");
include(ROOT_PATH . "/app/helpers/validateUser.php");

$errors = array();

$table = 'users';

$admin_users = selectAll($table, ['admin'=>1]);
$id = '';
$username = '';
$email = '';
$password = '';
$passwordConf = '';


function loginUser($user){
    $_SESSION['id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['admin'] = $user['admin'];
    $_SESSION['message'] = 'You are now logged in';
    $_SESSION['type'] = 'success';
    
    if ($_SESSION['admin']) {
        # code...
        header('location: ' . BASE_URL . '/admin/dashboard.php');
    } else {
        # code...
        header('location: ' . BASE_URL . '/index.php');
    }
    exit();
}

if (isset($_POST['register-btn']) || isset($_POST['create-admin'])){
    
    $errors = validateUser($_POST);

    if(count($errors) === 0){
        unset($_POST['register-btn'], $_POST['passwordConf'], $_POST['create-admin']);
        $_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        if (isset($_POST['admin'])) {
            # code...        
            $_POST['admin'] = 1;    
            $user_id = create($table, $_POST);
            $_SESSION['message'] = "Admin created successfully!";
            $_SESSION['type'] = "success";
            header('location: ' . BASE_URL . '/admin/users/index.php');
            exit();
        } else {
            # code...
            $_POST['admin'] = 0;    
            $user_id = create($table, $_POST);
            $user = selectOne($table, ['id' => $user_id]);
            loginUser($user); 
        }             
    }    
    else {
        $username = $_POST['username'];
        $admin = isset($_POST['admin']) ? 1 : 0;
        $email = $_POST['email'];
        $password = $_POST['password'];
        $passwordConf = $_POST['passwordConf'];
    }  
}

if (isset($_POST['update-user'])) {
    # code...    
    $errors = validateUser($_POST);

    if(count($errors) === 0){
        $id = $_POST['id'];
        unset($_POST['passwordConf'], $_POST['update-user'], $_POST['id']);
        $_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $_POST['admin'] = isset($_POST['admin']) ? 1: 0;    
        $count = update($table, $id, $_POST);
        $_SESSION['message'] = "Admin user created!";
        $_SESSION['type'] = "success";
        header('location: ' . BASE_URL . '/admin/users/index.php');
        exit();         
    }    
    else {
        $username = $_POST['username'];
        $admin = isset($_POST['admin']) ? 1 : 0;
        $email = $_POST['email'];
        $password = $_POST['password'];
        $passwordConf = $_POST['passwordConf'];
    }  

}

if(isset($_GET['id']))
{
    $user = selectOne($table, ['id' => $_GET['id']]);
    $id =  $user['id'];
    $username =  $user['username'];
    $admin = isset( $user['admin']) ? 1 : 0;
    $email = $user['email'];
}






if (isset($_POST['login-btn'])){
    $errors = validateLogin($_POST);

    if (count($errors) === 0){
        $user = selectOne($table, ['username' => $_POST['username']]);

        if ($user && password_verify($_POST['password'], $user['password'])) {
            # code...login, redirect
            loginUser($user);
        } else {
            # code...
            array_push($errors, 'Wrong credentials');
        }
    }

    $username = $_POST['username'];
    $password = $_POST['password'];
}