
<html>
    <head>
        <title>Messaging</title>
    </head>
<body>
    <h2>Post messages to 2 conversation</h2>
    <form action="/conversation/2/message" method="post">
        <input type="number" name="sender_id" value="2">
        <input type="number" name="receiver_id" value="1">
        <input type="text" name="text" value="test message">
        <input type="submit" value="Send">
    </form>
    <h2>Post messages to 1 conversation</h2>
    <form action="/conversation/1/message" method="post">
        <input type="number" name="sender_id" value="2">
        <input type="number" name="receiver_id" value="1">
        <input type="text" name="text" value="test message">
        <input type="submit" value="Send">
    </form>
</body>
</html>
