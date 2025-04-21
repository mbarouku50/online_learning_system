function addstu(){
    var studname = $("#studname").val()
    var studreg = $("#studreg").val()
    var stuemail = $("#stuemail").val()
    var stupass = $("#stupass").val()

    //test if it work in console
    // console.log(studname);
    // console.log(studreg);
    // console.log(stuemail);
    // console.log(stupass);

    $.ajax({
        url:'student/addstudent.php',
        method: 'POST',
        dataType: "json",
        data:{
            stusignup:"stusignup",
            studname: studname,
            studreg: studreg,
            stuemail: stuemail,
            stupass: stupass,
        },
        success:function(data){
            console.log(data);
            if(data == "Registration successful"){
                $('#successMsg').html("<span class='alert alert-succcess'>Regislation successful</span>");
            } else if(data == "failed"){
                $('#successMsg').html("<span class='alert alert-succcess'>unable to Register</span>");
            }
        },
    })
}
