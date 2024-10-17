# Nc Theming

A template to get started with Nextcloud app development.

## Usage

- To get started easily use the [Appstore App generator](https://apps.nextcloud.com/developer/apps/generate) to
  dynamically generate an App based on this repository with all the constants prefilled.
- Alternatively you can use the "Use this template" button on the top of this page to create a new repository based on
  this repository. Afterwards adjust all the necessary constants like App ID, namespace, descriptions etc.

Once your app is ready follow the [instructions](https://nextcloudappstore.readthedocs.io/en/latest/developer.html) to
upload it to the Appstore.

## Resources

### Documentation for developers:

- General documentation and tutorials: https://nextcloud.com/developer
- Technical documentation: https://docs.nextcloud.com/server/latest/developer_manual

### Help for developers:

- Official community chat: https://cloud.nextcloud.com/call/xs25tz5y
- Official community forum: https://help.nextcloud.com/c/dev/11

## Custom CSS

Custom CSS can be added via CSS Files in the `css` folder. The file content will be build into the `lib/Theme/CustomCss.php` file.

To build the CSS for the first time:
1. Copy the `lib/Theme/CustomCss.php.tmpl` file to `lib/Theme/CustomCss.php`.
2. run `make build_css`.

