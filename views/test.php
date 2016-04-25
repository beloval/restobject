<?php
include 'config.php';
include 'MessageManager.php';


class TestPostMessage{
    private $conn;

    function set_up($conn){
        $this->conn = $conn;
    }

    function assert_equals($actual, $expected){
        if($actual != $expected){
            throw new Exception("Assertion error!");
        }
    }

    function test_post_succeeds(){
        $message = Message::make(array(
            'sender'=> '2',
            'receiver'=> '1',
            'text'=> 'test message this is',
            'created_at'=> null,
        ));
        $conversation_id = 1;

        $message_manager = new MessageManager();
        $message_manager->set_connection($this->conn);
        $success = $message_manager->post_message($message, $conversation_id);

        $this->assert_equals($success, true);
    }

    function test_post_fails(){
        $message = Message::make(array(
            'sender'=> '2',
            'receiver'=> '1',
            'text'=> 'test message this is',
            'created_at'=> null,
        ));
        $conversation_id = 2;

        $message_manager = new MessageManager();
        $message_manager->set_connection($this->conn);
        $success = $message_manager->post_message($message, $conversation_id);

        $this->assert_equals($success, false);
    }
}

$test = new TestPostMessage();
$test->set_up(new mysqli( $DB_CONFIG['host'], $DB_CONFIG['username'], $DB_CONFIG['password'], $DB_CONFIG['db_name']));

$test_one = 'success';
$test_two = 'success';

try {
    $test->test_post_succeeds();
}catch(Exception $e){
    $test_one = 'fail';
}

try {
    $test->test_post_fails();
}catch(Exception $e){
    $test_two = 'fail';
}

?>

<html>
<head>
    <style>
        body{
            padding: 20px;
        }

        .test_method{
            width: 700px;
            font-size: 14px;
            padding: 4px;
            margin: 2px;
            font-family: 'sans-serif';
            color: black;
        }

        .test_method.success{
            color: black;
            background: #6eff91;
        }

        .test_method.fail{
            color: black;
            background: #ff5d54;
        }
    </style>
</head>
<body>
    <h3>Test posting messages</h3>
    <div class="test_method <?php echo $test_one; ?>">
        TestPostMessage::test_post_succeeds() - <?php echo $test_one; ?>
    </div>
    <div class="test_method <?php echo $test_two; ?>">
        TestPostMessage::test_post_fails() - <?php echo $test_two; ?>
    </div>
</body>
</html>
