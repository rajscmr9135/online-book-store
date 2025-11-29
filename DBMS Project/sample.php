<form name="fruitForm" method="post" action="other-post.php">
<label><input type="radio" name="fruit" value="apple" onClick="regularFruit()">apple</input></label>
<label><input type="radio" name="fruit" value="orange" onClick="regularFruit()">orange</input></label>
<label><input type="radio" name="fruit" value="lemon" onClick="regularFruit()">lemon</input></label>
<label onClick="otherFruit()">
    <input type="radio" name="fruit" id="other_fruit" value="other" >or other fruit:</input>
    <input type ="text" name="other" id="other_text"/></label>
    <input type="submit" value="Submit">
</form>

<script>
function otherFruit(){
a=document.getElementById('other_fruit');
a.checked=true;
}
function regularFruit(){
a=document.getElementById('other_text');
a.value="";
}
</script>
