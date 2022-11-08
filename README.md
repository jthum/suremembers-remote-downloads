# Remote Downloads for SureMembers
[SureMembers](https://suremembers.com/) allows you to upload and attach files to an access group in order to provide [secure digital downloads](https://suremembers.com/docs/how-to-secure-digital-downloads/) to members of that group. With this plugin activated, you can serve files from remote storage locations such as AWS S3 without revealing the source URLs to the end user. 

## Installation
To install this plugin, copy suremembers-remote-downloads.php to /wp-content/plugins/, and activate it under Dashboard > Plugins.

## Serving a file from a remote location
Create a plain text file that contains the remote URL as its contents, and save it as "your-remote-download.url.txt". The filename **must** end in ".url.txt", otherwise it works like a regular download. Upload and attach the text file to a SureMembers access group like any other file, and copy the private link that is generated. When you access the link, it proxies the download from the remote URL in the text file, instead of serving the text file itself.

## Requirements
This plugin relies on "[allow_url_fopen](https://www.php.net/manual/en/filesystem.configuration.php#ini.allow-url-fopen)", which is enabled by default, but some web hosts may disable the feature by setting allow_url_fopen to off in php.ini.

## Considerations
Even though the file is located elsewhere, keep in mind that your web server still has to process and serve each download on its own. As opposed to a local file, the server has to download data from the remote location before serving it to the end user. That is additional work for the server and it is not ideal if it has to handle a number of parallel requests. 

## Improvements
If the file is hosted on [AWS S3](https://aws.amazon.com/s3/) (or [Bunny Storage](https://bunny.net/storage/)), we can use [token authentication](https://support.bunny.net/hc/en-us/articles/360016055099-How-to-sign-URLs-for-BunnyCDN-Token-Authentication) to generate signed URLs that expire, so downloads can be handled directly by the storage service or a CDN. That will not only reduce the load on the web server but it can scale better and may result in faster downloads for the end user. A future version of this plugin can explore that option.