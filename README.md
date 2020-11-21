# Example Com Api Data Provider

For local development

```
docker build --tag example-com-api-data-provider:dev .
```
```
docker run -it -v "$(pwd)":/var/www --entrypoint <SOME BASH COMMAND> example-com-api-data-provider:dev <SOME BASH ARGS>
```

