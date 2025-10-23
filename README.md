# Martian Robots

Accepts grid size and robots placements with commands.
Robots move sequentially and are marked as LOST if they go out of bounds.
Scent markers track the last know position of a lost rover

## Coding pattern

Coding is done in one file for readibility and reference.

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