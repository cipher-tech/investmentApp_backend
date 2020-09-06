<html><body style='width:100%;background:white'>
  <div style='width:90%; height: auto; margin: auto;margin-top: 20px;border-radius: 5px;'>
  <div style='width:100%;'>

  <div style='background:#D3D3D3; padding:5px;'>
  <h3 style='padding: 1px;font-family: Georgia; text-align:center;'><span style='color:rgb(42, 128, 185);'>{{$details["companyName"]}} </span></h3>
  </div>

  <div style='background:rgba(44,130,201,1); padding:5px;  width:50%;margin:auto; margin-top:20px;'>
  <h3 style='padding: 1px;font-family: Georgia; text-align:center; color:white;'>{{$details["title"]}}</h3>
  </div>

  <div style='width:80%; margin:auto;'>

  <h4 style='padding: 1px;'>Dear {{$details["name"]}} </h4> 

  <br>
<br>

<div style='width:100%;height: auto; border-top: 1px solid #D3D3D3; margin: auto;border-radius: 6px;'>
<br>
    {{-- // <!-- inpput main content here --> --}}
    <p style='font-family:Arial, Helvetica, sans-serif;'><strong> {{$details["header"]}} </strong></p>
@foreach ($details["body"] as $item)
    <p style='font-family:Arial, Helvetica, sans-serif;'>{{$item}}</p>
    
@endforeach
<a href="{{$details["links"]["registerLink"]}}" target="_blank">{{$details["links"]["registerLink"]}}</a>
  </div>

<br>
<br>

{{-- // if didnt close well, add closing div here --}}
  <div style='background:#D3D3D3; padding:5px;'>

 <p style='text-align:center;'>
    <span style='color:rgb(42, 128, 185);'>{{$details["companyName"]}}</span> © 2020 All Rights Reserved</p>
</div>

   </div>
  </div>
  </body></html>


{{-- mail($to, $subject, $message, $headers); --}}