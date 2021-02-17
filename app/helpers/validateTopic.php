<?php

function validateTopic($topic)
{
    
    $errors = array();

    if(empty($topic['name'])){
        array_push($errors, 'Name is required');
    }

    $existingTopic = selectOne('topics', ['name' => $topic['name']]);
    if(isset($existingTopic))
    {
        if (isset($topic['update-topic']) && $existingTopic['id'] != $post['id']) {
            array_push($errors, 'Topic with that title already exists');
        }

        if (isset($topic['add-topic'])) {
            array_push($errors, 'Topic with that title already exists');
        }
    }
    return $errors;


}
