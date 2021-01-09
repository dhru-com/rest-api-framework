# rest-api-framework
Build REST APIs simply and auto generate postman documents.

[![Watch the video](https://i9.ytimg.com/vi/rwcHwYOsU7U/mq1.jpg?sqp=CPTF5v8F&rs=AOn4CLDypVsso9ix19BSj0a6ctTrDCkMiQ)](https://youtu.be/rwcHwYOsU7U)



###Getting started with config app 
```
src/v1/app.config.php
```

###Public access for an endpoint url
```
public/[endpoint base]/[api version]/index.php
```

###Endpoints base folder
```
src/v1/Endpoints/[ENDPOINT BASE]/[ENDPOINT NAME]
```

###Sync with POSTMAN 
```
Make command : make doc
```

###Create new endpoint
```
Make command : make endpoint
- Select Version
- Select Endpoint base
- Set endpoint name
Sample methods  will be created in a folder "src/ [API VERSION] / [ENDPOINT BASE] / [ENDPOINT NAME]"

Or

Simply copy folder "src/v1/Endpoints/backend/boilerplate"
```



