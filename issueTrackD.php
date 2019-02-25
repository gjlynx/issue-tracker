<?php
//LTC Issue/Task Tracking system
// adding for PHP4 - 13 Oct 2016
$action = $_REQUEST["action"];	
$issue_id = $_REQUEST["issue_id"];	
$gzUser = $_REQUEST["gzUser"];
$PHP_SELF = $_SERVER["PHP_SELF"];
$project = $_POST["project"];
echo "PHP SELF: "+$PHP_SELF;

//phpinfo(); 


// go read the cookies
//****************************************************************/

   $myCookies=split(";",getenv("HTTP_COOKIE"));
   $gzUser="";
   while (list($key, $val) = each($myCookies)) {
   if (ereg("gzUser",$val)){
     $startPos=strpos($val,"=");
     $authcode=rawurldecode(substr($val,$startPos+1));
     $authcode=str_replace("+"," ",$authcode);
     //echo $authcode;
     //echo $project;
}
}

$DEBUG="TRUExxx";

$projecturl=rawurlencode($project);
if ($DEBUG=="TRUE"){
$authcode="gjlynx@us.ibm.com";
}else
{
if($authcode=="" ) {
  echo "<a href=\"authuser.php?&authcode=\&parentID=$PHP_SELF\">Please Authenticate </a>";
  return;
}
}

//echo "... action ...";
//echo + $action;
function selectMail($project)

{
  global $db, $PHP_SELF, $myrow2,$authcode,$issue_id;
  $do_what2 ="SELECT user from userprojects where project ='".$project."'";
  //echo $do_what2;
   $result2 = mysql_query($do_what2,$db);

    printf("<p>Project Members to be notified by email <br>");
    printf( "<form action =$PHP_SELF method=post>");
 
    printf("<Select multiple name=email[] size=5 >\n");
 while ($myrow2 = mysql_fetch_array($result2)) {
       $selected="SELECTED";
       if ($myrow2["project"]==$project ){
         $selected="SELECTED";
       }
       printf("<option value='%s' %s >%s %s \n", $myrow2["user"],$selected, $myrow2["user"],$myrow2["project"]);
      }
    
     printf("</select><P>");

 //    print( "          <input type=\"submit\" name=\"action\" value=\"Send\" >";

     print "<input type=\"hidden\" name=\"issue_id\" value=\"$issue_id\"> ";
     print "<input type=\"hidden\" name=\"project\" value=\"$project\"> ";
     print ("<input type=submit name=action value=send_mail>");
     print("</form>");
}

function selectPriority($project)
{
  global $db, $PHP_SELF, $myrow2,$authcode,$priority;
  $do_what2 ="SELECT priority,description from projects where project ='".$project."'";
 // echo $do_what2;
   $result2 = mysql_query($do_what2,$db);
   $myrow2=mysql_fetch_array($result2);
   $status2=$myrow2["priority"];

   $status3=explode(",",$status2);

   printf("<Select name=priority size=1>\n");
   foreach ($status3 as  $key => $value) {
//     echo "Key: $key; Value: $value  <br>\n";
       if ($value==$priority){
         $selected="SELECTED";
       }else {
         $selected="";
       }
       printf("<option value='%s' %s> %s \n",$value ,$selected,$value );
   }
   printf("</select>");
}


function selectProject($project)
{
global $db, $PHP_SELF, $myrow2,$status,$authcode;
 // echo "project:  ".$project;
   $do_what2 = "SELECT p.project from projects p,userprojects u where p.project=u.project and user='$authcode'";
//   echo $do_what2;
   $result2 = mysql_query($do_what2,$db);

    printf("<Select name=project size=1>\n");

 //just in case they are not in this project
     $selected="SELECTED";
     printf("<option value='%s' %s >%s  \n", $project,$selected, $project);

      while ($myrow2 = mysql_fetch_array($result2)) {
       $selected="";
       //echo $myrow2["project"].$project;
       if ($myrow2["project"]==$project ){
         $selected="SELECTED";
       }
       printf("<option value='%s' %s >%s %s \n", $myrow2["project"],$selected, $myrow2["project"],$myrow2["description"]);
      }
    printf("</select>");
}

function selectStatus($project)
{

  global $db, $PHP_SELF, $myrow2,$authcode,$status;
  $do_what2 ="SELECT status,description from projects where project ='".$project."'";
//  echo "current status: ".$status;
//   echo $do_what2;
   $result2 = mysql_query($do_what2,$db);
   $myrow2=mysql_fetch_array($result2);
   $status2=$myrow2["status"];

$status3=explode(",",$status2);

 //  echo mysql_errno().": ".mysql_error()."<BR>";

    printf("<Select name=status size=1>\n");
   foreach ($status3 as  $key => $value) {
     //echo "Key: $key; Value: $value  <br>\n";
       if ($value==$status){
         $selected="SELECTED";
       }else {
         $selected="";
       }
       printf("<option value='%s' %s> %s \n",$value ,$selected,$value );
   }
   printf("</select>");
}
$urlproject=rawurlencode($project);
$db = mysql_connect("localhost", "root");
//mysql_select_db("develop",$db);
echo $table;
switch ($table){

case 'chase':
$WHERE= "and b.groupset=512 and makeexternal =1 and b.bug_status not in ('CLOSED','REJECTED','DEFERRED') and to_days(b.delta_ts)< (to_days(now())-14)";


// funky date function for mysql:-=>  date_format(creation_ts,'%m-%d-%Y')as Created
 
$do_what="SELECT bug_id as \"bug_id\",issue_id,bug_id as \"long_desc\",i.status as \"Issue Status\",i.finish_date as \"Date in Status\",bug_severity,bug_status,a.login_name as \"Assigned to\",r.login_name as \"Reporter\",
TO_DAYS(NOW()) - to_days(creation_ts) as Age, date_format(delta_ts,'%m-%d-%Y') as LastChange, 
short_desc,b.groupset   
FROM bugsltc.bugs b ,bugsltc.profiles a, bugsltc.profiles r  LEFT JOIN issues i ON i.task_id=b.bug_id
WHERE b.assigned_to=a.userid and b.reporter=r.userid   
 ".$WHERE ; 
break;

case 'issues':

$do_what="SELECT issue_id,project,status,priority,distro,originator,assigned_to,contacts,lastupdate comments  FROM issues";
break;
case 'ibmsusebugs' :
$distro="SuSE";
$do_what="SELECT bug_id,issue_id,issues.priority,opened,reporter,summary  FROM ibmsusebugs LEFT JOIN issues ON issues.task_id=ibmsusebugs.bug_id ";
break;
case 'ibmrhbugs' :
$distro="RedHat";
//$do_what="SELECT bug_id,issue_id,issues.priority,opened,reporter,summary  FROM ibmrhbugs LEFT JOIN issues ON issues.task_id=ibmrhbugs.bug_id WHERE distro!='RedHat'";
$do_what="SELECT bug_id,issue_id,opened,reporter,summary  FROM ibmrhbugs LEFT JOIN issues ON issues.task_id=ibmrhbugs.bug_id  ";

$do_what="SELECT task_id,issue_id,i.priority as LTC_Priority,r.priority as RH_Priority,distro,lastupdate,assigned_to,summary   FROM ibmrhbugs r LEFT JOIN issues i ON i.task_id=r.bug_id ";
$do_what="SELECT bug_id,issue_id,i.priority as LTC_Pri,r.priority as RH_Pri,distro,lastupdate,assigned_to,summary,comments,'  ',reporter   FROM ibmrhbugs r LEFT JOIN issues i ON i.task_id=r.bug_id ";
break;
default:
$WHERE=" WHERE 1=1  " ;
$WHERE=$WHERE." and  user='$authcode' ";
if ($project!=""){
$WHERE=$WHERE." and i.project='$project' ";
}


if ($action=="NOOP"){
print_style();
//echo "<html></html>";
return;
}


if ($distro=='RedHat'){
$WHERE=" WHERE distro='RedHat' ";
}
if ($distro=='SuSE'){
$WHERE=" WHERE distro='SuSE' ";
}

if ($distro=='TurboLinux'){
$WHERE=" WHERE distro='TurboLinux' ";
}

if ($distro=='Caldera'){
$WHERE=" WHERE distro='Caldera' ";
}

if($status!=""){
$WHERE=$WHERE." and status in ('$status')";
}

if($authcode!=""){
$WHERE=" $WHERE and user='$authcode' ";
}


$do_what="SELECT issue_id,task_id,nextaction,comments,i.lastupdate,status,priority,distro,assigned_to,i.project  FROM issues  \\
 i LEFT JOIN userprojects u ON i.project=u.project ".$WHERE;

}
//$do_what="SELECT bug_id,issue_id,opened,reporter,summary  FROM ibmrhbugs LEFT JOIN issues ON issues.task_id=ibmrhbugs.bug_id ";
//$do_what="SELECT issue_id,project,status,priority,distro,originator,assigned_to,contacts,lastupdate ,comments  FROM issues";

//if ($ORDERBY==""){ $ORDERBY="2,3,4";}
if ($action=='Query'){
 $action="query";
}
if ($action==''){
 $action="query";
}
//echo $action;
function print_style2(){
printf("        <style type=\"TEXT/CSS\"> \n" );
printf("        font.pagehead  { font-size:20pt; font-family:Helvetica, Helv, sans-serif; font-weight:600 }  \n");
printf("        font.whitebd   { font-size:16pt; font-family:\"comic sans ms\", Helvetica, Helv, sans-serif; font-weight:600 } \n");
printf("        font.listhead  { font-size:14pt; font-family:Helvetica, Helv, sans-serif; font-weight:600 }  \n");
printf("        font.head  { color:#0000ff; font-size:10pt; font-weight:600 } \n");
printf("        font.line  { color:black;   font-size:10pt; font-weight:600 } \n");
printf("        font.note  { color:black;   font-size:10pt; font-weight:500; background-color:#d0d0ff } \n");
printf("    </style> \n");

}

function print_style(){
printf("<head>\n");
printf(" <style> \n");
printf("a { text-decoration: none;\n");
printf("  color: #800000;\n");
printf("  font-weight: bold;\n");
printf("  }\n");
printf("td { font-family: tahoma,verdana,arial,helvetica,sans-serif;\n");
printf("   }\n");
printf(".price { font-family: tahoma,verdana,arial,helvetica,sans-serif;\n");
printf("        font-weight: bold;\n");
printf("         color:#339933 ;\n");
printf("   }\n");
printf("\n");
printf("  .item { font-family: tahoma,verdana,arial,helvetica,sans-serif;\n");
printf("      font-weight: normal;\n");
printf("      color: #800000;\n");
printf("      width: 30%;\n");
printf("   }\n");
printf("h4.desc { font-family: tahoma,verdana,arial,helvetica,sans-serif;\n");
printf("      font-weight: small;\n");
printf("      color: #000000;\n");
printf("      width: 40;\n");
printf("   }\n");
printf("</style>\n");
printf("</head>\n");
printf("<body BGCOLOR=#cccc99 LINK=#009933 VLINK=#009933>\n");

}





if($LOTUS=="true"){

$do_what="SELECT distro,platform, kernel, glibc,gcc,xfree86,java, nls, rel_date, lastupdate, prod_id
FROM products";
}


if ($action=="ad_hoc_query")
{
$do_what_adhoc=$do_what;
$do_what_adhoc="SELECT issue_id,project,comments \"desc\" FROM issues where status ='open' ";
//$do_what="SELECT bug_id,bug_severity,login_name,bug_status,creation_ts,
//TO_DAYS(NOW()) - to_days(creation_ts) as Age, product, version,short_desc, long_desc
//FROM bugs b,profiles p
//WHERE  b.assigned_to=p.userid and bug_status  not in ('CLOSED','RESOLVED')";


echo "<html>";
echo "<h3>LTC Issue/Task Tracking System    </h3>";
echo "<pre>";
echo "<form action =$PHP_SELF method=post>";
echo "       Select: <textarea name=\"do_what_adhoc\" rows=5 cols=90 >$do_what_adhoc </textarea> <br>";
echo "       Order by: ";
   // echo"<INPUT TYPE=radio NAME=ORDERBY VALUE=1 CHECKED> Product ";
  //  echo"<INPUT TYPE=radio NAME=ORDERBY VALUE=2 > Platform";
 //   echo"<INPUT TYPE=radio NAME=ORDERBY VALUE=10 > Release Date";
    echo"<br>";                      
//    echo"        <INPUT TYPE=checkbox NAME=TABLESTRUCT VALUE=TRUE > Show bugs Table Structure";

echo "<br>";
echo "          <input type=\"hidden\" name=\"do_what2\" value=\"$do_what\"> ";
echo "          <input type=\"submit\" name=\"action\" value=\"query\" >";
echo "</form>";


}

if ($action=='Delete'){
    printf ("<form action=$PHP_SELF method=POST>");
    printf ("Please confirm that you want to delete issue: %s",$issue_id);
    printf ("<br><font color=green><input type=submit name=action value=ConfirmDelete></font>");
    printf ("<br><font color=red size=-1><input type=hidden name=issue_id  value=$issue_id></font>");
    printf ("<br><font color=red size=-1><input type=submit name=actionX onclick=back() value=Cancel></font>");
    printf("</form>");

 }

if ($action=='ConfirmDelete'){

$do_what_del="DELETE from issues where issue_id=".$issue_id;
echo "Executing -=> ". $do_what_del;
echo "<br>";
mysql_select_db("bugsp",$db);
$result=mysql_query($do_what_del,$db);
   echo mysql_errno().": ".mysql_error()."<BR>";
//echo $result;
$action='query';
}
if ($action=='Insert'){
$do_what_ins="INSERT into issues (project,nextaction,task,task_id,reference,status,distro,originator,assigned_to,contacts,start_date,target_date,finish_date,comments) values('$project','$nextaction','$task','$task_id','$reference','$status','$distro','$originator','$assigned_to','$contacts',current_date,'$target_date','$finish_date','$comments')" ;

//echo "<br>".$do_what_ins;

mysql_select_db("bugsp",$db);
$result=mysql_query($do_what_ins,$db);
$issue_id=mysql_insert_id();
printf("Result: %s new record # %s", $result,$issue_id);
//printf("Result: %s new record # %s", $result,mysql_insert_id());
   echo mysql_errno().": ".mysql_error()."<BR>";
//echo $result;

if ($mail_action=='send_mail'){

$action="send_mailXXX";
echo $project;
selectMail($project);

//echo "sending the mail ...to: ".$assigned_to." ".$contacts."\n";
}

//$action="query";
}
if ($action=='Update'){

//$db = mysql_connect("localhost", "root");
mysql_select_db("bugsp",$db);
$date= (date("l d M Y h:i:s A"));
//$comments=$comments."\n"."by-> ".$authcode." on ".$date."<--- \n";
$comments=$comments."by-> ".$authcode." on ".$date."<--- \n";

//$reference=htmlspecialchars($reference);
$reference=rawurlencode($reference);
$task=rawurlencode($task);
if ($status!=$oldstatus){

echo "Status: To ->".$status."  From ->".$oldstatus; 
$comments=$comments."\nStatus: To ->".$status."  From ->".$oldstatus; 

$date_in_status=date("Y-m-d");
}
if ($nextaction!=$oldnextaction){
echo $nextaction;
$comments=$comments.$nextaction."\n";
}

$comments=addslashes($comments);


//if ($task_id==''){
$do_what_up ="UPDATE issues set reference_id=$reference_id,task_id=$task_id,nextaction='$nextaction',project='$project',priority='$priority', task='$task', reference='$reference',status='$status', distro='$distro', originator='$originator',assigned_to='$assigned_to',contacts='$contacts', start_date='$start_date', target_date='$target_date',finish_date='$date_in_status',lastupdate=current_date,comments='$comments'  WHERE issue_id=$issue_id ";
//}else
//{
//$do_what_up ="UPDATE issues set reference_id=$reference_id,nextaction='$nextaction',project='$project', priority='$priority',task='$task',task_id=$task_id, reference='$reference',status='$status', distro='$distro', originator='$originator',assigned_to='$assigned_to',contacts='$contacts', start_date='$start_date', target_date='$target_date',finish_date='$date_in_status',lastupdate=current_date,comments='$comments'  WHERE issue_id=$issue_id ";
//} 
//echo $do_what_up;
$result=mysql_query($do_what_up,$db);
echo mysql_errno().": ".mysql_error()."<BR>";
//$action="query";
$action="add_edit";

if ($mail_action=='send_mail'){

$action="send_mailXXX";
echo $project;
selectMail($project);

//echo "sending the mail ...to: ".$assigned_to." ".$contacts."\n";
}




}

if ($action=='send_mail'){
//phpinfo();
//$count=count($email);

//for ($i=0; $i<$count; $i++) {
//    echo "emails: ".$email[$i];
//}

 $do_what2 ="SELECT comments,project,reference_id,contacts,assigned_to,originator from issues where issue_id =".$issue_id;
 //echo $do_what2;
 mysql_select_db("bugsp",$db);
   $result2 = mysql_query($do_what2,$db);
   $myrow2=mysql_fetch_array($result2);
   $comments=$myrow2["comments"];
   $reference_id=$myrow2["reference_id"];
   $contacts=$myrow2["contacts"];
   $assigned_to=$myrow2["assigned_to"];
   $originator=$myrow2["originator"];

$email_address="";
if ($email!=""){
$email_address=implode(",",$email);
}
//echo "email_address: ".$email_address;
$message="The following bug has been reported in IBM LTC Bugilla:\r\n" ."\r\n \r\n$bug_text_edit";
//$from="From: Glen Johnson - IBM Corp<gjohnson@austin.ibm.com>\r\nBcc:<gjlynx@hotmail.com>\r\n" ;
if ($contacts!=""){
$contacts=$contacts.",".$email_address;
}else{
$contacts=$email_address;
}
$from="From: Glenzilla <".$authcode .">\r\ncc:".$contacts.",".$originator."\r\n" ;
//$message="a simple one liner ...";
 echo "sending the mail ...to: ".$assigned_to." ".$contacts." ".$originator."\n";
$subject ="Issue Tracker ".$issue_id;
//echo ($recipient);
//echo $subject;
//echo $message;
//echo $assigned_to;
//echo $orignator;
$projurl=rawurlencode($project);
if ($project=="TheChase"){
$comments=$comments."\n http://bugzilla.linux.ibm.com/show_bug.cgi?id=$reference_id \n";
}else{
$comments=$comments."\n http://$HTTP_HOST$PHP_SELF?&action=add_edit&issue_id=$issue_id&project=$projurl";
}
//$from= rawurlencode($from);
$comments=$comments.$bug_text_edit;
//echo $comments;

$rc=mail($assigned_to, $subject ,$comments,$from);
if ($rc==1)echo "Success....";
$action="add_edit";

//$action="query";
//$table="chase";
//$issue_id=;

}


if ($action=='add_edit'){
//$db = mysql_connect("localhost", "root");
mysql_select_db("bugsp",$db);

$do_what="SELECT issue_id,nextaction,comments,project,reference,reference_id,task,task_id,status,priority,distro,originator,assigned_to,contacts,start_date,target_date,finish_date as \"date_in_status\" ,lastupdate  from issues";
$do_what = $do_what." WHERE issue_id=$issue_id";
echo $do_what;
echo $db;
 echo "<br>issue_id".$issue_id;


$result=mysql_query($do_what,$db);

 $myrow = mysql_fetch_array($result);
 $Fields = mysql_num_fields($result);

    print_style();

       print "<h3><center><a <a href=http://$HTTP_HOST/myglenzilla.php> Issue/Task Tracking System</a>
       <font color=red>#$myrow[issue_id]</font> <font color=green> - $myrow[project]  </font> </center>    </h3>";

    print ("<form action=$PHP_SELF method=POST>");
    print("<table width='45%' cellpadding=0 cellspacing=0 border=0 bgcolor=#ffcc99>\n");
    print("<input type=hidden name=issue_id value=$myrow[issue_id]>");
    //start id $i=1 to skip over issue_id
    for ($i=2; $i < $Fields-0; $i++){
       $myfield=mysql_field_name($result,$i);
       if ($myfield=='comments') {
        if ($issue_id==0){
        printf("<td></td><td></td><td align='left' rowspan=15><textarea name=comments cols=75 rows=30   >%s</textarea></td>", rawurldecode($comments) );
         }else{
         $oldnextaction=$myrow[nextaction];
         printf("<td></td><td></td><td align='left' rowspan=15 nowrap>Next Action<input type=text name='nextaction' value='$myrow[nextaction]' size=65><br><textarea name=comments cols=75 rows=30   >%s</textarea></td>", $myrow[$i]);
       } 
       }else{
 
       printf("<tr>\n") ;
       printf("<td color='#ffffff' bgcolor='#ffff66' align='right' nowrap>%s </td>", $myfield);

       switch ( mysql_field_name( $result,$i)){
       case 'nextaction':

          printf("<td colspan=3><input type=text name=\"%s\" value=\"%s\" ></td>",$myfield, $myrow[$i]);
        break;
       case 'date_in_status':
         $laststatusdate=$myrow[$i];
        printf("<td align='left'><input type=text name=\"%s\" value=\"%s\" ></td>",$myfield, $myrow[$i]);
        break;
       case 'desc':

         printf("<td nowrap>%s&nbsp&nbsp</td>",nl2br($myrow[$i]) );
         break;               
     //  case 'projectSelect':
       case 'project':
        if ($issue_id==0){
         //printf("<td align='left'><input type=text name=\"%s\" value=\"%s\" ></td>",$myfield, $project);
         printf("<td align='left'>");
         selectProject($project);
         printf("</td >");
         }else{
         // printf("<td align='left'><input type=text name=\"%s\" value=\"%s\" ></td>",$myfield, $myrow[$i]);
          printf("<td align='left'>");
          selectProject($myrow["project"]);
          printf("</td >");
         }
        break;
       case 'status':   
         $oldstatus=$myrow[$i];
         $status=$myrow[$i];

         printf("<td align='left'>");
         if ($issue_id==0){
           selectStatus($project);
          }else{
         selectStatus($myrow["project"]);
         }
         printf("</td >");
       break;
       case 'priority':
         $priority=$myrow[$i];
         printf("<td align='left'>");
         if ($issue_id==0){
           selectPriority($project);
           }else{
         selectPriority($myrow["project"]);
         }
         printf("</td >");
         break;
       case 'task_id':
        if ($issue_id==0){
        printf("<td align='left'><input type=text name=\"%s\" value=\"%s\" ></td>",$myfield, $bug_id);
        }else{ 
        printf("<td align='left'><input type=text name=\"%s\" value=\"%s\" ></td>",$myfield, $myrow[$i]);
        }
        break;

       case 'reference_id':
        if ($issue_id==0){
        printf("<td align='left'><input type=text name=\"%s\" value=\"%s\" ></td>",$myfield, "0");
 //       printf("<td align='left'><input type=text name=\"%s\" value=\"%s\" ></td>",$myfield, $bug_id);
        }else{ 
        printf("<td align='left'><input type=text name=\"%s\" value=\"%s\" ></td>",$myfield, $myrow[$i]);
        }
        break;
       case 'originator':
        if ($issue_id==0){
        printf("<td align='left'><input type=text name=\"%s\" value=\"%s\" ></td>",$myfield, $authcode);
        }else{ 
        printf("<td align='left'><input type=text name=\"%s\" value=\"%s\" ></td>",$myfield, $myrow[$i]);
        }
        break;
        
       case 'distro':
        if ($issue_id==0){
        printf("<td align='left'><input type=text name=\"%s\" value=\"%s\" ></td>",$myfield, $distro);
        }else{ 
        printf("<td align='left'><input type=text name=\"%s\" value=\"%s\" ></td>",$myfield, $myrow[$i]);
        }
        break;
       case 'task':
        printf("<td align='left'><input type=text name=\"%s\" value=\"%s\" ><br>%s </td>",$myfield, rawurldecode($myrow[$i]),rawurldecode($myrow[$i]));
        printf("</tr>");
        break;
       case 'reference':
        printf("<td align='left'><input type=text name=\"%s\" value=\"%s\" ><br>%s </td>",$myfield, rawurldecode($myrow[$i]),rawurldecode($myrow[$i]),$myrow['reference_id']);
        printf("</tr>");
        break;
       case 'issue_idX':
        printf("<td align='left'> %s </td>", $myrow[$i]);
        print("<input type=hidden name=issue_id value=$myrow[$i]");
        break;
       
       case 'issue_id':
        //printf("<td align='left'> %s </td>", $myrow[$i]);
        break;
       case 'lastupdate':
        printf("<td align='left'> %s </td>", $myrow[$i]);
        break;
       default :
        printf("<td align='left'><input type=text name=\"%s\" value=\"%s\" ></td>",$myfield, $myrow[$i]);
        printf("</tr>");
       } //end switch
 
       } //end else
    }
   print("<input type=hidden name=prod_id value=$prod_id>");
   print("<input type=hidden name=table value=$table>");
   print("<input type=hidden name=oldstatus value=$oldstatus>");
   print("<input type=hidden name=oldnextaction value='$oldnextaction'>");
   print("</table>"); 

   if ($issue_id==0){
    print ("<input type=submit name=action value=Insert>");
    print ("[send mail on Insert <input type=radio name=mail_action value=send_mail >yes <input type=radio name=mail_action value=''checked>No]");
   }else{
    print ("<input type=submit name=action value=Update>");
    print ("<input type=submit name=action value=Insert>");
    print ("<input type=submit name=action value=Delete>");
    print ("<input type=submit name=action value=Query>");
    print ("[send mail on Update <input type=radio name=mail_action value=send_mail >yes <input type=radio name=mail_action value=''checked>No]");
  //  echo "<a href=javascript:location.reload()>Back 3 screens  </a>"; 
    //echo "<a href=javascript:history.go(-3);location.reload()>Back 3 screens  </a>"; 
   echo "<a href=javascript:history.go(-3)>Back 3 screens  </a>"; 
   }
   print("</form>");
}

if ($action=='compose_mail-OLD'){
echo "form to email bug ..\n";
$bug_text_edit=(rawurldecode($bug_text));
echo "<form action=$PHP_SELF method=post>";
echo "Recipient: <input type=\"text\" name=\"assigned_to\" value=\"gjohnson@austin.ibm.com\" size=35>"; 
echo "  Subject: <input type=\"text\" name=\"subject\" value=\"IBM LTC Bugillza Bug - $bug_id\" size=35 >"; 

echo " <TEXTAREA NAME=\"bug_text_edit\" COLS=\"95\" ROWS=\"30\">$bug_text_edit </textarea> ";
echo " <br>         <input type=\"submit\" name=\"action\" value=\"send_mail\" >";
echo "</form>";
}

if ($action=='compose_mail'){
echo "<h4>Form to email a nasty gram to a bugzilla bug owner   </h4> ..\n";
$do_what_descs=" SELECT thetext from bugsltc.longdescs where bug_id= ".$bug_id;
$do_what_fields=" SELECT bug_id,product,short_desc,component,version,op_sys,priority,bug_severity,rep_platform from bugsltc.bugs  where  bug_id= ".$bug_id;
echo $do_what_descs;
$result=mysql_query($do_what_descs,$db);

 while ($myrow = mysql_fetch_array($result)) {
 $bug_text_edit=$bug_text_edit.$myrow["thetext"];
 }
 
//echo $do_what_fields;
$result=mysql_query($do_what_fields,$db);

 while ($myrow = mysql_fetch_array($result)) {

//un set it all
$bug_field ="\nA review of bugs in our LTC Bugzilla shows that the bug you are working on needs your attention. 
This bug has not had any activity within the last 2 weeks or more. Can you please review this bug and follow the
link above to the LTC Bugzilla. Rather than updating me, please provide your update directly to the bugzilla record. 

Thank you very much. 
\n";

}
//$bug_text_edit=(rawurldecode($bug_text));
$bug_text_edit =$bug_field."\n\n".$bug_text_edit;
//echo "<form action=$PHP_SELF method=post>";
echo "<pre>";
//echo "<form action=http://iserieslinux.austin.ibm.com:80/bugzMail.php method=post>";
echo "<form action=$PHP_SELF method=post>";
echo "Recipient: <input type=\"text\" name=\"assigned_to\" value=\"$assigned_to\" size=35>";
//echo "From: <input type=\"text\" name=\"from\" value=\"gjohnson@austin.ibm.com\" size=35><br>";
echo "From: <input type=\"text\" name=\"from\" value=\"khake@us.ibm.com\" size=35><br>";
echo "  Subject: <input type=\"text\" name=\"subject\" value=\"IBM LTC Bugillza Bug - $bug_id\" size=35: >";
echo "<table>";
echo "<td valign=top>Component <br>";

echo " <br> <font color=green>        <input type=\"submit\" name=\"action\" value=\"send_mail\" > </font>";
echo "</td>";

echo "<td valign=top>Version <br>";
echo "</td>";
echo "<td>";

echo " <TEXTAREA NAME=\"bug_text_edit\" COLS=\"95\" ROWS=\"30\">$bug_text_edit </textarea> ";
//echo " <br>         <input type=\"submit\" name=\"action\" value=\"send_mail\" >";
echo "<p>";
echo "          <input type=\"hidden\" name=\"bug_id\" value=\"$bug_id\"> ";
echo "          <input type=\"hidden\" name=\"issue_id\" value=\"$issue_id\"> ";
echo "</form>";
echo "</td>";
echo "</table>";


}


if ($action=='query'){

if( ($do_what_adhoc)!="")
{
$do_what=$do_what_adhoc;
}

 $db = mysql_connect("localhost", "root");
  mysql_select_db("bugsp",$db);

// echo $ORDERBY;
if ($ORDERBY!=""){ 
 $do_what=$do_what."  ORDER BY ".$ORDERBY;
 }
 
 $do_what = stripslashes($do_what);

echo "<br>".$do_what_adhoc;


if ($TABLESTRUCT=="TRUE"){
print "<table border='1' width='100%'><tr>";
$fields = mysql_list_fields("bugsp", "bugs");
                            $columns = mysql_num_fields($fields);

                          for ($i = 0; $i < $columns; $i++) {
                          $name  = mysql_field_name  ($fields, $i);
                          $type  = mysql_field_type  ($fields, $i);
                          $len   = mysql_field_len   ($fields, $i);
                          $flags = mysql_field_flags ($fields, $i);
                          echo "<tr><td>". $name."<td> ".$type."<td> ".$len."<td> ".$flags."</tr>";

                          }

echo "</table>";
echo "<p><p>";
}

//echo $do_what;

$result=mysql_query($do_what,$db);
   $numRows = mysql_NumRows($result);
 if ($numRows == 0)
    { print_style();
     printf("<b><br>This query found no records!....</b>\n");

   }

   else
   {
 $Fields = mysql_num_fields($result);
//echo "number of fields -->".$Fields."..\n";
print "<center><h3>Issue/Task Tracking System  -        </h3></center>";
    print_style();
    print("<table width='100%' cellpadding=0 cellspacing=0 border=0 bgcolor=#ffcc99>\n");


           // Build Column Headers - last field is prod_id - don't need to see
   
           for ($i=0; $i < $Fields; $i++){
         printf("<th color='#ffffff' bgcolor='#ffff66' align='left'><a href=$PHP_SELF?&action=query&ORDERBY=%s&distro=$distro&table=$table&project=%s >%s&nbsp$nbsp</a></th>",  mysql_field_name( $result,$i),$projecturl,mysql_field_name($result,$i) );
           }
           printf("</tr>");

 while ($myrow = mysql_fetch_array($result)) {

             
//      print(" <tr height=1 bgcolor=#cccccc><td><img src='clear_dot.gif' height=1 width=1></td></tr>");
      print(" <tr height=1 bgcolor=#cccccc><td></td></tr>");
            print "<tr>";
           
           for($i=0; $i < $Fields; $i++){
 
             switch ( mysql_field_name( $result,$i)){
            
             case 'desc':
              printf("<td nowrap>%s&nbsp&nbsp</td>",nl2br($myrow[$i]) );
              break;               
             case 'task_id':
                   
              if ($myrow["distro"]=='SuSE'){
              printf("<td><a href=http://bugzilla.suse.de/show_bug.cgi?id=%s>%s</a ></td>",$myrow[$i],$myrow[$i] );
              }
              if ($myrow["distro"]=='RedHat'){
              printf("<td><a href=https://bugzilla.redhat.com/bugzilla/show_bug.cgi?id=%s>%s</a ></td>",$myrow[$i],$myrow[$i] );
              }
              if ($myrow["distro"]=='TurboLinux'){
                print("<td>&nbsp</td>");
              }
              if ($myrow["distro"]=='Caldera'){
                print("<td>&nbsp</td>");
              }
              
              if ($myrow["distro"]==''){
                print("<td>&nbsp</td>"); }else{
               // print("<td>&nbsp</td>");
              
              }
              break;
             case 'bug_id':
              if ($table=='ibmsusebugs'){
                   
              printf("<td><a href=http://bugzilla.suse.de/show_bug.cgi?id=%s>%s</a >",$myrow[$i],$myrow[$i] );
              }
              if ($table=='ibmrhbugs'){
              printf("<td><a href=https://bugzilla.redhat.com/bugzilla/show_bug.cgi?id=%s>%s</a >",$myrow[$i],$myrow[$i] );
              }
              if ($table=='chase'){
              printf("<td><a href=http://bugzilla.linux.ibm.com/show_bug.cgi?id=%s>%s</a >",$myrow[$i],$myrow[$i] ); 
              }
              break;

              
             case 'long_desc':
              $tomail=$myrow['Assigned to'];
              if($myrow['bug_status']=='FIXEDAWAITINGTESTxxx'){
               $tomail=$myrow['Reporter'];
              }
              if($myrow['bug_status']=='SUBMITTEDxxxx'){
               $tomail=$myrow['Reporter'];
              }
              if($myrow['bug_status']=='TESTEDxxxxxx'){
               $tomail=$myrow['Reporter'];
              }
            printf("<td nowrap><a href=%s?&action=compose_mail&bug_id=%s&assigned_to=%s&issue_id=%s>SEND BUG</a >",$PHP_SELF,
              $myrow['bug_id'],$tomail,$myrow[issue_id] );
              break;
             case 'kernel':
              printf("<td class='price'>%s </td>",$myrow[$i]);
              break;
             case 'issue_id':
               if ($myrow[$i]!=NULL){
              printf("<td class='item'><a href=%s?&action=add_edit&issue_id=%s>%s</a></td>",
               $PHP_SELF,$myrow['issue_id'],$myrow[$i]);
               }else{

                
      printf("<td nowrap><a href=$PHP_SELF?&action=add_edit&issue_id=0&bug_id=%s&distro=%s&comments=%s&reporter=%s&table=$table>Add Issue &nbsp</a >",$myrow['bug_id'],$distro,rawurlencode(substr($myrow['summary'],0,60)),$myrow['reporter'] );
               }
              break;
             case 'nextaction':
              printf("<td class='price' nowrap>&nbsp %s</td>",$myrow[$i]  );
              break;
             case 'comments':
              printf("<td class='item' nowrap>%s</td>",substr($myrow[$i],0,15)  );
              break;
             default:
              if($myrow[$i]==""){
             printf("<td nowrap>%s&nbsp -</td>",$myrow[$i] );
             }else{
             printf("<td nowrap>%s&nbsp</td>",$myrow[$i] );
             }
             //printf("<td nowrap>%s&nbsp&nbsp.</td>",$myrow[$i] );

             }

           }

           print "</tr>";
 }
           print "</table>";
}
print "<br> click on issue id to EDIT or do: <br> ";
print"<a href=$PHP_SELF?&action=ad_hoc_query>adhoc query </a> | ";
print "<a href=https://$HTTP_HOST/index.php>home  </a><br>";
print "<a href=https://$HTTP_HOST$PHP_SELF?&action=add_edit&issue_id=0&project=$urlproject>New Issue  </a><br>";
phpinfo();
return;


}

?>
