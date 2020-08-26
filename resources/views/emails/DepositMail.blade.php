<!DOCTYPE html>
<html>
<head>
    <title>{{$details['title']}}</title>
</head>
<body>
    <h1>{{ $details['title'] }}</h1>

    <p>
        Hey, {{ $details['name'] ? $details['name'] : ""}}
    </p>
    <p>{{ $details['body'] }}</p>
   
    <p>Thank you</p>
</body>
</html>