# Martian Robots

Accepts grid size and robots placements with commands.
Robots move sequentially and are marked as LOST if they go out of bounds.
Scent markers track the last know position of a lost rover

## Assumptions:
I assume the instructions means lines from the input and the coordinates x and y.
I throw an exception when these are reached
For ease of use I've defaulted to use the input input.txt

## Next steps

The next interation is adding a move back  

## Run Martian Robots

Default using input.txt supplied in code
``` php app.php ```
Using external text file
``` php app.php test.txt ```

## Docker Compose

If you don't have php installed you might have docker. keep in mind if installing an external input file you have to file running in docker needs to be in the app root
``` docker-compose run --rm php ```

``` docker-compose run --rm php php app.php test.txt ```

## UnitTest

I unit tested the Grid, Position and the Robot classes

``` composer test ```

Or if you don't have php here are the docker commands

``` docker-compose run --rm martian-robots-test ```