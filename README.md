# Back-end for ArcTouch Movies WebApp

In this repository you'll find the back-end for the ArcTouch movies WebApp.

There are 3 endpoints in it:

- /movies/upcoming
    - List the upcoming movies ordered by their names in an alphabetical manner
- /movies/{movieId}
    - The details of a specific movie identified by the {movieId} parameter. e.g. `/movies/detail/123456`
- /movies/query?q=<query>
    - List the movies matching a specific query. e.g `/movies/query?q=Thor: Ragnarok`
    
## Architecture

The code is divided in the following manner:

- config (framework specific configuration)
- public/index.php (front controller)
- src (all the PHP code)
    - Controller
        - The code that receives a Request and returns a Response. Here live the classes that are called for each endpoint
    - EventListener
        - The code responsible for executing when some event happens on the applciation. e.g When an exception (or error) is thrown
    - Helper
        - The auxiliary code to help classes with domain logic to fulfill their purposes
    - Model
        - The classes that represent something in the real domain model. e.g.: Movie, MovieList, etc
    - Repository
        - The code responsible for fetching (from wherever it is) the Model data
- tests (the unit tests for the src code)

### Repositories

The repositories have the most important job in this application: Retrieve the data that is necessary for the application.

This data is fetched mainly from the TMDb API but also has a caching system implemented.
The cache can be accessed through any PSR-6 implementation. 

Since it's necessary to fetch not only the first 20 movies as the TMDb API returns in this API endpoints multiple HTTP Requests can be executed.

This was the followed approach due to the simplicity of the domain. If we had larger set of data, pagination would be implemented.

The genres repository only has to fetch the genres once and then all the genres can be accessed through its ID without making extra HTTP Requests.
This is **similar** to the Flyweight pattern.

### Model

The movies lists were divided in two categories:

- A regular movie list that contains only a list of movies
- An upcoming movies list that contains a lst of movies and the date interval where they were searched from

Also a SpecificDate class was created so we can treat DateTime values without having to worry about time and so they could be represented as string in a simple manner.

### Helpers

Some helper traits were created so the code could be simpler and reused among other classes.

The *PropertyAccessTrait* allows private members to be accessed directly without a *getter* but if a *getter* is implemented for the property in question it is used.

Also a *MovieFactory* was developed so the parsing of the result from the API could be in a central place.

A *MovieDbApiResponseParserTrait* was created so the response from the API could be parsed from a code that could be reused in all the repositories.

## Assumptions

If a movie has the *poster_path* available it is used as its image. Otherwise the *backdrop_path* is used. If none of them are available, the *imagePath* of a movie is *NULL*.

The search for movies was not implemented from the upcoming movies list. This way the user can search movies from any release date.
Also the query was used as it is in the API. The movie list isn't filtered to show only the movies that have the query in their names specifically. 

## Libraries used

- Symfony
    - Symfony is a simple yet powerful and extensible framework for web applications so it was used to improve productivity.
    - The Symfony Components were used for:
        - Routing (and Controllers)
        - Dependency Injection (that can be seen in the `config/services.yaml` file)
        - Configuration (e.g. *ParameterBagInterface*  used in the *MovieDbApiResponseParserTrait*)
        - Caching (since it implements the PSR-6 and can be changed to any other implementation)
    - Most of the code (the core part) is still framework agnostic
- Guzzle
    - Guzzle is the most famous PHP HTTP Client. Since Symfony HTTP Client will only be stable in the 4.3 version (still in beta), it makes completally sense to use Guzzle which is a very stable tool.
    - It was used to execute the HTTP Requests to the TMDb API. Its use can be seen in the *MovieDbApiResponseParserTrait*
-  PHPUnit
    - PHPUnit is the leading tool for Unit Tests with PHP.
    - Inside the `tests` folder reside all the unit tests written for the application.
