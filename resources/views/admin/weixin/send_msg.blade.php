<form action="sendmsg" method="post">
    {{csrf_field()}}
    <input type="text" name="msg">
    <input type="submit" value="SUBMIT">
</form>