# Notification Hubs REST wrapper for PHP

[![Build Status](https://scrutinizer-ci.com/g/webwarejp/notificationhubs-rest-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/webwarejp/notificationhubs-rest-php/)

This is a implementation of a REST wrapper using the [REST APIs of Notification Hubs](http://msdn.microsoft.com/en-us/library/dn495827.aspx) from a PHP back-end.

## How to use the code above
Initialize your Notification Hubs client (substitute the connection string and hub name as instructed in the [Get started tutorial](http://azure.microsoft.com/en-us/documentation/articles/notification-hubs-ios-get-started/)):

    $hub = new NotificationHub("connection string", "hubname"); 

Then add the send code depending on your target mobile platform.

### iOS

    $factory = new NotificationFactory();
    $notification = $factory->createNotification("apple", "Hello from PHP!");
    $hub->sendNotification($notification);

### Android

    $factory = new NotificationFactory();
    $notification = $factory->createNotification("gcm", "Hello from PHP!");
    $hub->sendNotification($notification);

## Notes
This code is provided as-is with no guarantees.
