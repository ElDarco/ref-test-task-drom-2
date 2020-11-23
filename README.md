# Example Com Api Data Provider

Данный пакет реализует ApiDataProvider для сервиса example.com
Особенностью данного пакета является:
1. Поддержка PSR-7
2. Поддержка PSR-17
3. Поддержка PSR-18
4. Изоляция создания запросов, транспорта и парсинга ответов, от основной части приложения и бизнес логики. 
5. Масштабируемость, открытость для кастомизации.

Пример иниализации:
```
//Создание сборщика запросов
//Где $requestFactory и $streamFactory реализация  PSR-17 фабрик, которую имплементирует ваш проект/фреймворк
$requestBuilder = new ExampleComApiDataProvider/RequestBuilder($requestFactory, $streamFactory);

//Создание обработчика ответов
//Где ResponseHandler является реализацией ResponseHandlerInterface
$responseHandler = new ExampleComApiDataProvider/ResponseHandler();

//Создание транспорта
//Где HttpClientPSR18 является любым реализованным или используемым вами клиентом имплементирующим PSR-18
$httpClient = new HttpClientPSR18();

//Создание самого провайдера
$dataProvider = new ExampleComApiDataProvider/DataProvider($httpClient, $requestBuilder, $responseHandler);
```

Вспомогательные классы:

Comment::class - класс реализующий интерфейс CommentInterface, 
представляет из себя объект для хранения данных и представление бизнес сущности

CommentCollection::class - класс реализующий интерфейс CommentCollectionInterface, 
представляет из себя объект для хранения множества CommentInterface, для итерации оных

----

Создения контейнера для локальной разработке в Docker
```
docker build --tag example-com-api-data-provider:dev .
```
Команда для выполнения комманд в контейнере
```
docker run -it -v "$(pwd)":/var/www --entrypoint <SOME BASH COMMAND> example-com-api-data-provider:dev <SOME BASH ARGS>
```
Например:

Установка зависимостей
```
docker run -it -v "$(pwd)":/var/www --entrypoint composer example-com-api-data-provider:dev install
```
Запуск тестов
```
docker run -it -v "$(pwd)":/var/www --entrypoint composer example-com-api-data-provider:dev unit-test-run
```