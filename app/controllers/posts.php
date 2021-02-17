<?php

include(ROOT_PATH . "/app/database/db.php");
include(ROOT_PATH . "/app/helpers/validatePost.php");

$table = 'posts';

$topics = selectAll('topics');
$posts = selectAll($table);

$errors = array();
$title = "";
$body = "";
$topic_id = "";
$published = "";

if (isset($_GET['id'])) {
    # code...
    $post = selectOne($table, ['id' => $_GET['id']]);
    $id = $post['id'];
    $title = $post['title'];
    $body = $post['body'];
    $topic_id = $post['topic_id'];
    $published = $post['published'];
}


if (isset($_GET['delete_id'])) {
    # code...
    $count = delete($table, $_GET['delete_id']);

    $_SESSION['message'] = 'Post deleted successfully';
    $_SESSION['type'] = 'success';
    header("location: " . BASE_URL . "/admin/posts/index.php");
    exit();
}

if (isset($_GET['published']) && isset($_GET['p_id'])) {
    # code...
    $published = $_GET['published']; 
    $p_id = $_GET['p_id'];
    $count = update($table, $p_id, ['published' => $published]);
    $_SESSION['message'] = 'Post published state changed!';
    $_SESSION['type'] = 'success';
    header("location: " . BASE_URL . "/admin/posts/index.php");
    exit();
}

if (isset($_POST['add-post'])) {
    // dd($_FILES['image']['name']);
    $errors = validatePost($_POST);

    if (!empty($_FILES['image']['name'])) {
        # code...
        $image_name = time() . '_' .  $_FILES['image']['name'];
        $destination = ROOT_PATH . "/assets/images/" . $image_name;

        $result = move_uploaded_file($_FILES['image']['tmp_name'], $destination);

        if ($result) {
            # code...
            $_POST['image'] = $image_name;
        } else {
            # code...
            array_push($errors, "Failed to upload image");
        }
        
    } else {
        # code...
        array_push($errors, "Post image required");
    }
    

    if (count($errors) == 0) {
        # code...
        unset($_POST['add-post']);
        $_POST['user_id'] = $_SESSION['id'];
        $_POST['published'] = isset($_POST['published']) ? 1 : 0;
        $_POST['body'] = htmlentities($_POST['body']);

        $post_id = create($table, $_POST);
        $_SESSION['message'] = 'Post created successfully';
        $_SESSION['type'] = 'success';
        header("location: " . BASE_URL . "/admin/posts/index.php");
        exit();

    } else {
        $title = $_POST['title'];
        $body = $_POST['body'];
        $topic_id = $_POST['topic_id'];
        $published = isset($_POST['published']) ? 1 : 0;
    }

}

if (isset($_POST['update-post'])) {
    # code...
    $errors = validatePost($_POST);

    if (!empty($_FILES['image']['name'])) {
        # code...
        $image_name = time() . '_' .  $_FILES['image']['name'];
        $destination = ROOT_PATH . "/assets/images/" . $image_name;

        $result = move_uploaded_file($_FILES['image']['tmp_name'], $destination);

        if ($result) {
            # code...
            $_POST['image'] = $image_name;
        } else {
            # code...
            array_push($errors, "Failed to upload image");
        }
        
    } else {
        # code...
        array_push($errors, "Post image required");
    }

    if (count($errors) == 0) {
        # code...
        $id = $_POST['id'];
        unset($_POST['update-post'], $_POST['id']);
        $_POST['user_id'] = $_SESSION['id'];
        $_POST['published'] = isset($_POST['published']) ? 1 : 0;
        $_POST['body'] = htmlentities($_POST['body']);

        $post_id = update($table, $id, $_POST);
        $_SESSION['message'] = "Post updated successfully";
        $_SESSION['type'] = "success";
        header("location: " . BASE_URL . "/admin/posts/index.php");  

    } else {
        $title = $_POST['title'];
        $body = $_POST['body'];
        $topic_id = $_POST['topic_id'];
        $published = isset($_POST['published']) ? 1 : 0;
    }
}