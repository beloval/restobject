<?php
error_reporting(E_ERROR|E_ALL);
include 'config.php';
include 'AltoRouter.php';
include 'MessageManager.php';

// DB connection
$conn = new mysqli($DB_CONFIG['host'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['db_name']);

if ($conn->connect_error) {
    die("DB Connection failed: " . $conn->connect_error);
}

// Routing

$router = new AltoRouter();

$router->map( 'GET', '/', function() {
    include 'views/home.php';
});

$message_manager = new MessageManager();
$message_manager->set_connection($conn);
$GLOBALS['message_manager'] = $message_manager;

//   /user/{user_id}/conversation/{conversation_id}
$router->map( 'GET', '/user/[i:user_id]/conversation/[i:conversation_id]', function( $user_id, $conversation_id) {
    $message_manager = $GLOBALS['message_manager'];
    $conversation = $message_manager->get_conversation($user_id, $conversation_id);
    if($conversation == null){
        header("HTTP/1.1 403 Forbidden");
    }else {
        $data = array(
            'conversation' => $conversation,
        );
        header('Content-Type: application/json');
        echo json_encode($data);
    }
});

// /conversation/{conversation_id}/message
$router->map( 'POST', '/conversation/[i:conversation_id]/message', function( $conversation_id ) {
    $message_manager = $GLOBALS['message_manager'];
    $message = Message::make(array(
        'receiver'=>$_POST['receiver_id'],
        'sender'=>$_POST['sender_id'],
        'text'=>$_POST['text'],
        'created_at'=>null,
        ));
    $success = $message_manager->post_message($message, $conversation_id);
    if($success){
        header("HTTP/1.1 201 Created");
    }else{
        header("HTTP/1.1 403 Forbidden");
    }
});

$match = $router->match();

if( $match && is_callable( $match['target'] ) ) {
    call_user_func_array( $match['target'], $match['params'] );
} else {
    header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
}