<?php
$con = mysqli_connect("localhost","root", "1234");
mysqli_query($con, "set names 'utf8'");
if(! $con) {
    die("Wrong in connect into base database!");
}

mysqli_select_db($con, "202102_wqy_mis");

if ($_POST['command'] == "studentScoreSemester") {
    if($_POST['semester'] == "0") {
        $sql = "SELECT wqy_courseandstudent.StudentNum,wqy_student.Sname,wqy_course.Cname,wqy_course.OpeningSemester,wqy_courseandstudent.Score
FROM wqy_courseandstudent,wqy_student,wqy_course
WHERE wqy_courseandstudent.StudentNum = wqy_student.Sno
AND wqy_courseandstudent.CourseNum = wqy_course.Cno;";
    } else {
        $sql = "SELECT semester{$_POST['semester']}.StudentNum,wqy_student.Sname,semester{$_POST['semester']}.Cname,semester{$_POST['semester']}.OpeningSemester,semester{$_POST['semester']}.Score
FROM semester{$_POST['semester']},wqy_student
WHERE semester{$_POST['semester']}.StudentNum = wqy_student.Sno;";
    }
//
    $retval = mysqli_query($con, $sql);
    if(! $retval) {
        die("Wrong in select data from picture!" . mysqli_error($con));
    }

    $data = "{";

    $row = mysqli_fetch_array($retval);
    for ($i = 0; $row; $i++) {
//    var_dump($row);
        $data .= "\"Sno{$i}\":\"{$row['StudentNum']}\",
    \"Sname{$i}\":\"{$row['Sname']}\",
    \"Cname{$i}\":\"{$row['Cname']}\",
    \"Semester{$i}\":\"{$row['OpeningSemester']}\",
    \"Score{$i}\":\"{$row['Score']}\",
    ";
        $row = mysqli_fetch_array($retval);
    }
    $data = rtrim($data);
    $data = substr($data, 0, -1);
    $data .= "}";
    echo json_encode($data);
//    echo '{"result":"success"}';
} else if ($_POST['command'] == "studentTotalCredit") {
    $sql = "SELECT DISTINCT wqy_courseandstudent.StudentNum,wqy_student.Sname,wqy_course.OpeningSemester
    FROM wqy_courseandstudent,wqy_student,wqy_course
    WHERE wqy_courseandstudent.CourseNum = wqy_course.Cno
    AND wqy_courseandstudent.StudentNum = wqy_student.Sno
    AND wqy_course.OpeningSemester = '".$_POST['semester']."';";

    $retval = mysqli_query($con, $sql);

    $data = "{";
    $row = mysqli_fetch_array($retval);
    for ($i = 0 ; $row; $i++) {
        //调用储存结构来得到学生总学分
        $sql = "CALL stuTotalCredit('{$row['StudentNum']}', '{$row['OpeningSemester']}', @get);";

        mysqli_query($con, $sql);
        $sqlTC = mysqli_query($con, "select @get;");
        $totalCredit = mysqli_fetch_array($sqlTC);

//        var_dump($row);
        $data .= "\"Sno{$i}\":\"{$row['StudentNum']}\",
    \"Sname{$i}\":\"{$row['Sname']}\",
    \"Semester{$i}\":\"{$row['OpeningSemester']}\",
    \"TotalCredit{$i}\":\"{$totalCredit[0]}\",
    ";
        $row = mysqli_fetch_array($retval);
    }

    $data = rtrim($data);
    $data = substr($data, 0, -1);
    $data .= "}";
//    echo $data;
    echo json_encode($data);
} else if ($_POST['command'] == "CAveScore") {
    if($_POST['semester'] == "全学年") {
        $sql = "SELECT wqy_course.Cno,wqy_course.Cname,wqy_course.OpeningSemester, averagescore.AverageScore
FROM wqy_course,averagescore
WHERE averagescore.Cno = wqy_course.Cno
ORDER BY wqy_course.Cno;";
    } else {
        $sql = "SELECT wqy_course.Cno,wqy_course.Cname,wqy_course.OpeningSemester, averagescore.AverageScore
FROM wqy_course,averagescore
WHERE averagescore.Cno = wqy_course.Cno
AND wqy_course.OpeningSemester = '{$_POST['semester']}'
ORDER BY wqy_course.Cno;";
    }

    $retval = mysqli_query($con, $sql);

    $data = "{";
    $row = mysqli_fetch_array($retval);
    for ($i = 0 ; $row; $i++) {
//        var_dump($row);
        $data .= "\"Cno{$i}\":\"{$row['Cno']}\",
    \"Cname{$i}\":\"{$row['Cname']}\",
    \"Semester{$i}\":\"{$row['OpeningSemester']}\",
    \"AverageScore{$i}\":\"{$row['AverageScore']}\",
    ";
        $row = mysqli_fetch_array($retval);
    }

    $data = rtrim($data);
    $data = substr($data, 0, -1);
    $data .= "}";
//    echo $data;
    echo json_encode($data);
} else if ($_POST['command'] == 'stuClass') {
    if($_POST['semester'] == "全学年") {
        $sql = "SELECT classwithstudent.CourseNum,wqy_course.Cname,classwithstudent.Ccstudent,wqy_student.Sname,wqy_course.OpeningSemester,wqy_course.Credit
FROM wqy_course,wqy_student,classwithstudent
WHERE classwithstudent.Ccstudent = wqy_student.Sno
AND classwithstudent.CourseNum = wqy_course.Cno
ORDER BY classwithstudent.Ccstudent;";
    } else {
        $sql = "SELECT classwithstudent.CourseNum,wqy_course.Cname,classwithstudent.Ccstudent,wqy_student.Sname,wqy_course.OpeningSemester,wqy_course.Credit
FROM wqy_course,wqy_student,classwithstudent
WHERE classwithstudent.Ccstudent = wqy_student.Sno
AND classwithstudent.CourseNum = wqy_course.Cno
AND wqy_course.OpeningSemester = '{$_POST['semester']}'
ORDER BY classwithstudent.Ccstudent;";
    }

//    echo $sql;
    $retval = mysqli_query($con, $sql);

    $data = "{";
    $row = mysqli_fetch_array($retval);
    for ($i = 0 ; $row; $i++) {
//        var_dump($row);
        $data .= "\"Cno{$i}\":\"{$row['CourseNum']}\",
    \"Cname{$i}\":\"{$row['Cname']}\",
    \"Sno{$i}\":\"{$row['Ccstudent']}\",
    \"Sname{$i}\":\"{$row['Sname']}\",
    \"Semester{$i}\":\"{$row['OpeningSemester']}\",
    \"Credit{$i}\":\"{$row['Credit']}\",
    ";
        $row = mysqli_fetch_array($retval);
    }

    $data = rtrim($data);
    $data = substr($data, 0, -1);
    $data .= "}";
//    echo $data;
    echo json_encode($data);
} else if ($_POST['command'] == "teacherClass") {
    $sql = "SELECT wqy_course.Cno,wqy_course.Cname,wqy_teacher.Tno,wqy_teacher.Tname
FROM wqy_course,wqy_teacher,wqy_courseandteacher
WHERE wqy_courseandteacher.CourseNum = wqy_course.Cno
AND wqy_courseandteacher.TeacherNum = wqy_teacher.Tno
ORDER BY wqy_course.Cno;";

//    echo $sql;
    $retval = mysqli_query($con, $sql);

    $data = "{";
    $row = mysqli_fetch_array($retval);
    for ($i = 0 ; $row; $i++) {
//        var_dump($row);
        $data .= "\"Cno{$i}\":\"{$row['Cno']}\",
    \"Cname{$i}\":\"{$row['Cname']}\",
    \"Tno{$i}\":\"{$row['Tno']}\",
    \"Tname{$i}\":\"{$row['Tname']}\",
    ";
        $row = mysqli_fetch_array($retval);
    }

    $data = rtrim($data);
    $data = substr($data, 0, -1);
    $data .= "}";
//echo $data;
    echo json_encode($data);
} else if ($_POST['command'] == 'courseClass') {
    $sql = "SELECT wqy_courseandclass.CourseNum,wqy_course.Cname,wqy_courseandclass.ClassNum
FROM wqy_course,wqy_courseandclass
WHERE wqy_courseandclass.CourseNum = wqy_course.Cno
ORDER BY wqy_course.Cno;";

//echo $sql;
    $retval = mysqli_query($con, $sql);

    $data = "{";
    $row = mysqli_fetch_array($retval);
    for ($i = 0 ; $row; $i++) {
//        var_dump($row);
        $data .= "\"Cno{$i}\":\"{$row['CourseNum']}\",
    \"Cname{$i}\":\"{$row['Cname']}\",
    \"Ccno{$i}\":\"{$row['ClassNum']}\",
    ";
        $row = mysqli_fetch_array($retval);
    }

    $data = rtrim($data);
    $data = substr($data, 0, -1);
    $data .= "}";
//echo $data;
    echo json_encode($data);
} else if ($_POST['command'] == "ATCredit") {
    $sql = "SELECT * FROM studentatcredit;";

//echo $sql;
    $retval = mysqli_query($con, $sql);

    $data = "{";
    $row = mysqli_fetch_array($retval);
    for ($i = 0 ; $row; $i++) {
//        var_dump($row);
        $data .= "\"Sno{$i}\":\"{$row['StudentNum']}\",
    \"Sname{$i}\":\"{$row['Sname']}\",
    \"ATCredit{$i}\":\"{$row['ATCredit']}\",
    ";
        $row = mysqli_fetch_array($retval);
    }

    $data = rtrim($data);
    $data = substr($data, 0, -1);
    $data .= "}";
//echo $data;
    echo json_encode($data);
} else if ($_POST['command'] == 'allStuScore') {
    $sql = "SELECT wqy_courseandstudent.StudentNum, wqy_student.Sname, wqy_courseandstudent.CourseNum, wqy_course.Cname, wqy_courseandstudent.Score
FROM wqy_courseandstudent, wqy_student, wqy_course
WHERE wqy_courseandstudent.CourseNum = wqy_course.Cno
AND wqy_courseandstudent.StudentNum = wqy_student.Sno
ORDER BY wqy_courseandstudent.StudentNum;";

//echo $sql;
    $retval = mysqli_query($con, $sql);

    $data = "{";
    $row = mysqli_fetch_array($retval);
    for ($i = 0 ; $row; $i++) {
//        var_dump($row);
        $data .= "\"Sno{$i}\":\"{$row['StudentNum']}\",
    \"Sname{$i}\":\"{$row['Sname']}\",
    \"Cno{$i}\":\"{$row['CourseNum']}\",
    \"Cname{$i}\":\"{$row['Cname']}\",
    \"Score{$i}\":\"{$row['Score']}\",
    ";
        $row = mysqli_fetch_array($retval);
    }

    $data = rtrim($data);
    $data = substr($data, 0, -1);
    $data .= "}";
//echo $data;
    echo json_encode($data);
} else if ($_POST['command'] == "getScore") {
    $sql = "SELECT wqy_courseandstudent.Score 
FROM wqy_courseandstudent
WHERE wqy_courseandstudent.StudentNum = '{$_POST['Sno']}'
AND wqy_courseandstudent.CourseNum = '{$_POST['Cno']}';";

//echo $sql;
    $retval = mysqli_query($con, $sql);

    $data = "{";
    $row = mysqli_fetch_array($retval);
//        var_dump($row);
    $data .= "\"Score\":\"{$row['Score']}\"";
    $row = mysqli_fetch_array($retval);

    $data .= "}";
//echo $data;
    echo json_encode($data);
} else if ($_POST['command'] == 'updateScore') {
    $sql = "UPDATE wqy_courseandstudent
SET Score={$_POST['newScore']}
WHERE wqy_courseandstudent.StudentNum = '{$_POST['Sno']}'
AND wqy_courseandstudent.CourseNum = '{$_POST['Cno']}';";

//echo $sql;
    $retval = mysqli_query($con, $sql);

    $data = '{"result":"success"}';
//echo $data;
    echo json_encode($data);
}
mysqli_close($con);
