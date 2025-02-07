# ping-me-on-slack

Get notifications on Slack when changes are made on your WP website.

[![Coverage Status](https://coveralls.io/repos/github/badasswp/ping-me-on-slack/badge.svg?branch=master)](https://coveralls.io/github/badasswp/ping-me-on-slack?branch=master)

![screenshot-1](https://github.com/user-attachments/assets/667ab2fb-24c1-4d4d-ae83-5a89fc848710)

## Download

Download from [WordPress plugin repository](https://wordpress.org/plugins/ping-me-on-slack/).

You can also get the latest version from any of our [release tags](https://github.com/badasswp/ping-me-on-slack/releases).

## Getting Started

To get started, you would need to have an incoming webhook of your own. Head over to the URL below and follow the instructions to generate your webhook:

```
https://api.slack.com/messaging/webhooks
```

If you have done this successfully, you should have something that looks like so:

```
https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX
```

Save these details into your __Ping Me On Slack__ options page and you are ready to go!

![ping-me-on-slack](https://github.com/badasswp/ping-me-on-slack/assets/149586343/1da2be61-ab22-42ac-bf9b-63df814c093d)

## Why Ping Me On Slack?

Ever needed to keep track of what's happening on your website? No need to look further. It does a fantastic job of logging every single activity on your website to your preferred Slack channels. It is simple and fast!

* Get notified when a user creates, modifies or deletes a Post, Page or Image.
* Get notified when a user creates, or posts a Comment.
* Get notified when a user installs or activates, a Theme or Plugin.
* Get notified when a user creates, modifies or deletes a User.
* Get notified when a user logs in & out.

### Hooks

#### `ping_me_on_slack_dispatcher`

This custom hook (filter) provides the ability to customise the global Slack client. For e.g. To use your own custom Slack client, you can do:

```php
use MySlackClient;

add_filter( 'ping_me_on_slack_dispatcher', [ $this, 'slack_client' ], 10, 1 );

public function slack_client( $client ) {
    return new MySlackClient();
}
```

**Parameters**

- client _`{\PingMeOnSlack\Interfaces\Dispatcher}`_ By default this will be the global Slack client class that implements the `Dispatcher` interface.
<br/>

#### `ping_me_on_slack_{$post_type}_client`

This custom hook (filter) provides the ability to customise the Slack client. For e.g. To send with a custom username, you could do:

```php
add_filter( 'ping_me_on_slack_post_client', [ $this, 'post_client' ], 10, 1 );

public function post_client( $client ) {
    $client->args = wp_parse_args(
        [
            'username' => 'John Doe'
        ],
        $client->args
    )

    return $client;
}
```

**Parameters**

- client _`{\PingMeOnSlack\Core\Client}`_ By default this will be the Client instance.
<br/>

#### `ping_me_on_slack_{$post_type}_message`

This custom hook (filter) provides the ability to add a custom `Post` message to be sent to your Slack Workspace. For e.g. To send a custom message when a Post draft is created by a user, you could do:

```php
add_filter( 'ping_me_on_slack_post_message', [ $this, 'post_message' ], 10, 3 );

public function post_message( $message, $post, $event ): string {
    if ( 'draft' === $event ) {
        $message = sprintf(
            'Attention: A Post draft with ID: %d was just created, by %s!',
            $post->ID,
            get_user_by( 'id', $post->post_author )->display_name
        );
    }

    return (string) $message;
}
```

**Parameters**

- message _`{string}`_ By default this will be the passed message.
- post _`{WP_Post}`_ By default this will be the WP Post object.
- event _`{string}`_ By default this will be the event that just happened.
<br/>

#### `ping_me_on_slack_form_fields`

This custom hook (filter) provides a way to implement new Admin fields in the plugin's admin page. For e.g. To add a new text field, you could do:

```php
add_filter( 'ping_me_on_slack_form_fields', [ $this, 'custom_field' ] );

public function custom_field( $fields ): array {
    return wp_parse_args(
        [
            'telephone' => [
                'type'  => 'text',
                'name'  => 'ping_me_on_slack_telephone',
                'html'  => esc_html__( 'My Telephone', 'ping-me-on-slack' ),
                'label' => esc_html__( '(555) 555-1234', 'ping-me-on-slack' ),
            ]
        ],
        $fields
    );
}
```

**Parameters**

- fields _`{mixed[]}`_ By default this will be an associative array, containing fields options.
<br/>

#### `ping_me_on_slack_comment_client`

This custom hook (filter) provides the ability to customise the Slack client. For e.g. To send with a custom username, you could do:

```php
add_filter( 'ping_me_on_slack_comment_client', [ $this, 'comment_client' ], 10, 1 );

public function comment_client( $client ) {
    $client->args = wp_parse_args(
        [
            'username' => 'John Doe'
        ],
        $client->args
    )

    return $client;
}
```

**Parameters**

- client _`{\PingMeOnSlack\Core\Client}`_ By default this will be the Client instance.
<br/>

#### `ping_me_on_slack_comment_message`

This custom hook (filter) provides the ability to add a custom `Comment` message to be sent to your Slack Workspace. For e.g. To send a custom message when a Comment is trashed by a user, you could do:

```php
add_filter( 'ping_me_on_slack_comment_message', [ $this, 'comment_message' ], 10, 3 );

public function comment_message( $message, $comment, $event ): string {
    if ( 'trash' === $event ) {
        $message = sprintf(
            'Attention: A Comment with ID: %d was just trashed, by %s!',
            $comment->ID,
            wp_get_current_user()->display_name
        );
    }

    return (string) $message;
}
```

**Parameters**

- message _`{string}`_ By default this will be the passed message.
- comment _`{WP_Comment}`_ By default this will be the WP Comment object.
- event _`{string}`_ By default this will be the event that just happened.
<br/>

#### `ping_me_on_slack_on_ping_error`

This custom hook (action) fires immediately after the Ping call is made and an exception is thrown. For e.g. you can capture errors here like so:

```php
add_action( 'ping_me_on_slack_on_ping_error', [ $this, 'log_ping_errors' ], 10, 1 );

public function log_ping_errors( $e ): void {
    if ( $e instanceOf \Exception ) {
        wp_insert_post(
            [
                'post_type'   => 'ping_me_on_slack_logs',
                'post_title'  => sprintf(
                  'Fatal Error: %s'
                  (string) $e->getMessage()
                ),
            ]
        )
    }
}
```

**Parameters**

- e _`{Exception}`_ By default this will be an Exception.
<br/>

#### `ping_me_on_slack_login_client`

This custom hook (filter) provides the ability to customise the Slack client. For e.g. To send with a custom username, you could do:

```php
add_filter( 'ping_me_on_slack_login_client', [ $this, 'login_client' ], 10, 1 );

public function login_client( $client ) {
    $client->args = wp_parse_args(
        [
            'username' => 'John Doe'
        ],
        $client->args
    )

    return $client;
}
```

**Parameters**

- client _`{\PingMeOnSlack\Core\Client}`_ By default this will be the Client instance.
<br/>

#### `ping_me_on_slack_login_message`

This custom hook (filter) provides the ability to add a custom `Login` message to be sent to your Slack Workspace. For e.g. To send a custom message when an Administrator logs in, you could do:

```php
add_filter( 'ping_me_on_slack_login_message', [ $this, 'login_message' ], 10, 2 );

public function login_message( $message, $user ): string {
    if ( in_array( 'administrator', $user->roles, true ) ) {
        $message = sprintf(
            'Attention: An Administrator with ID: %d just logged in!',
            $user->ID
        );
    }

    return (string) $message;
}
```

**Parameters**

- message _`{string}`_ By default this will be the passed message.
- user _`{WP_User}`_ By default this will be the WP User object.
<br/>

#### `ping_me_on_slack_logout_client`

This custom hook (filter) provides the ability to customise the Slack client. For e.g. To send with a custom username, you could do:

```php
add_filter( 'ping_me_on_slack_logout_client', [ $this, 'logout_client' ], 10, 1 );

public function logout_client( $client ) {
    $client->args = wp_parse_args(
        [
            'username' => 'John Doe'
        ],
        $client->args
    )

    return $client;
}
```

**Parameters**

- client _`{\PingMeOnSlack\Core\Client}`_ By default this will be the Client instance.
<br/>

#### `ping_me_on_slack_logout_message`

This custom hook (filter) provides the ability to add a custom `Logout` message to be sent to your Slack Workspace. For e.g. To send a custom message when an Administrator logs out, you could do:

```php
add_filter( 'ping_me_on_slack_logout_message', [ $this, 'logout_message' ], 10, 2 );

public function logout_message( $message, $user ): string {
    if ( in_array( 'administrator', $user->roles, true ) ) {
        $message = sprintf(
            'Attention: An Administrator with ID: %d just logged out!',
            $user->ID
        );
    }

    return (string) $message;
}
```

**Parameters**

- message _`{string}`_ By default this will be the passed message.
- user _`{WP_User}`_ By default this will be the WP User object.
<br/>

#### `ping_me_on_slack_theme_client`

This custom hook (filter) provides the ability to customise the Slack client. For e.g. To send with a custom username, you could do:

```php
add_filter( 'ping_me_on_slack_theme_client', [ $this, 'theme_client' ], 10, 1 );

public function theme_client( $client ) {
    $client->args = wp_parse_args(
        [
            'username' => 'John Doe'
        ],
        $client->args
    )

    return $client;
}
```

**Parameters**

- client _`{\PingMeOnSlack\Core\Client}`_ By default this will be the Client instance.
<br/>

#### `ping_me_on_slack_theme_message`

This custom hook (filter) provides the ability to add a custom `theme` message to be sent to your Slack Workspace. For e.g. To send a custom message when a user switches a theme:

```php
add_filter( 'ping_me_on_slack_theme_message', [ $this, 'theme_message' ], 10, 2 );

public function theme_message( $message, $user ): string {
    if ( in_array( 'administrator', $user->roles, true ) ) {
        $message = sprintf(
            'Attention: An Administrator with ID: %d just logged out!',
            $user->ID
        );
    }

    return (string) $message;
}
```

**Parameters**

- message _`{string}`_ By default this will be the passed message.
- theme _`{WP_Theme}`_ By default this will be the WP Theme object.
<br/>

#### `ping_me_on_slack_user_creation_client`

This custom hook (filter) provides the ability to customise the Slack client. For e.g. To send with a custom username, you could do:

```php
add_filter( 'ping_me_on_slack_user_creation_client', [ $this, 'user_creation_client' ], 10, 1 );

public function user_creation_client( $client ) {
    $client->args = wp_parse_args(
        [
            'username' => 'John Doe'
        ],
        $client->args
    )

    return $client;
}
```

**Parameters**

- client _`{\PingMeOnSlack\Core\Client}`_ By default this will be the Client instance.
<br/>

#### `ping_me_on_slack_user_creation_message`

This custom hook (filter) provides the ability to add a custom `user_creation` message to be sent to your Slack Workspace. For e.g. To send a custom message when a User is created, you could do:

```php
add_filter( 'ping_me_on_slack_user_creation_message', [ $this, 'user_creation_message' ], 10, 2 );

public function user_creation_message( $message, $user_id ): string {
    $message = sprintf(
        'Attention: A User with ID: %d was just created!',
        $user_id
    );

    return (string) $message;
}
```

**Parameters**

- message _`{string}`_ By default this will be the passed message.
- user_id _`{int}`_ By default this will be the User ID.
<br/>

#### `ping_me_on_slack_user_deletion_client`

This custom hook (filter) provides the ability to customise the Slack client. For e.g. To send with a custom username, you could do:

```php
add_filter( 'ping_me_on_slack_user_deletion_client', [ $this, 'user_deletion_client' ], 10, 1 );

public function user_deletion_client( $client ) {
    $client->args = wp_parse_args(
        [
            'username' => 'John Doe'
        ],
        $client->args
    )

    return $client;
}
```

**Parameters**

- client _`{\PingMeOnSlack\Core\Client}`_ By default this will be the Client instance.
<br/>

#### `ping_me_on_slack_user_deletion_message`

This custom hook (filter) provides the ability to add a custom `user_deletion` message to be sent to your Slack Workspace. For e.g. To send a custom message when a User is created, you could do:

```php
add_filter( 'ping_me_on_slack_user_deletion_message', [ $this, 'user_deletion_message' ], 10, 2 );

public function user_deletion_message( $message, $user_id ): string {
    $message = sprintf(
        'Attention: A User with ID: %d was just deleted!',
        $user_id
    );

    return (string) $message;
}
```

**Parameters**

- message _`{string}`_ By default this will be the passed message.
- user_id _`{int}`_ By default this will be the User ID.
<br/>

#### `ping_me_on_slack_user_modification_client`

This custom hook (filter) provides the ability to customise the Slack client. For e.g. To send with a custom username, you could do:

```php
add_filter( 'ping_me_on_slack_user_modification_client', [ $this, 'user_modification_client' ], 10, 1 );

public function user_modification_client( $client ) {
    $client->args = wp_parse_args(
        [
            'username' => 'John Doe'
        ],
        $client->args
    )

    return $client;
}
```

**Parameters**

- client _`{\PingMeOnSlack\Core\Client}`_ By default this will be the Client instance.
<br/>

#### `ping_me_on_slack_user_modification_message`

This custom hook (filter) provides the ability to add a custom `user_modification` message to be sent to your Slack Workspace. For e.g. To send a custom message when a User is created, you could do:

```php
add_filter( 'ping_me_on_slack_user_modification_message', [ $this, 'user_modification_message' ], 10, 2 );

public function user_modification_message( $message, $user_id ): string {
    $message = sprintf(
        'Attention: A User with ID: %d was just modified!',
        $user_id
    );

    return (string) $message;
}
```

**Parameters**

- message _`{string}`_ By default this will be the passed message.
- user_id _`{int}`_ By default this will be the User ID.
<br/>
