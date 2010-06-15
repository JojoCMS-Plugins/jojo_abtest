Jojo_abtest Plugin
==================

This plugin implements basic AB Testing (aka Split testing) into Jojo, using Google Analytics for reporting. The logic behind this is very simple, but it's important to understand what this plugin is doing and how to read the reporting data.

This plugin does 2 things:

- Randomly assigns each visitor a number determining which version of a page the user sees. This number is saved in a cookie so they will always see the same variation when they return.
- Includes pageTracker._setCustomVar() javascript code for each tracked variable in the Google Analytics javascript at the bottom of the page.

The plugin relies on Custom Variables in Google Analytics. If you are already using custom variables for some other purpose, you will need to be careful that this plugin does not interfere with your existing reporting. Google currently allows you to track up to 5 custom variables at a time. These are set using the index numbers 1-5, and it's up to you to decide how you use these. If you aren't already using custom variables, and don't plan to run more than 5 AB tests at a time, then this limitation shouldn't be a problem.

How to use:
===========
1. First, install the plugin.

2. Decide what you wish to test. You will need a control version of the page (the current version) and one or more variations to test. We recommend sticking to only one or two variations unless you know what you are doing. For the purposes of this example code, we will be testing a variation of the heading on the homepage.

3. Decide if your test needs to run on every page, or just specific pages. In our homepage heading example, this will only need to run on the homepage. If you are testing out a different style for a sidebar newsletter form, this will likely need to run sitewide.

4. Add the following PHP code to global.php of your Theme. If this file does not exist, create it (you will need to visit domain.com/setup/ after creating this file).

<?php
if ($page->id == 1) {
    Jojo_Plugin_jojo_abtest::addTest(1, 'homepage_heading', 2); // Arguments: Custom variable number, name of test for reports, number of variations to test (including control)
}
?>
The above code is straightforward to explain - the IF statement only executes the code if the page ID is "1" which is another way of saying "the home page". The AddTest function registers a new test - in this case the arguments refer to using custom variable slot 1 (you have slots 1 - 5 available), a name of 'homepage_heading' (this is a human-readable name for the variable that will show up in Analytics reporting), and "2" means there are 2 different variations being tested - the control and the new variation.
It's important that the name argument contains no special characters other than underscores - otherwise you will run into issues in the next step.

5. Adjust your template(s) so that the variations are programatically included. Note the variation numbers are 0 indexed, so the number will be 0 or 1 rather than 1 or 2.

{if $ab.homepage_heading.variation == 1}
<h1>My New Heading</h1>
{else}
<h1>My Original Heading</h1>
{/if}

If you are editing template.tpl, you may need to restrict which pages your changes apply to eg.
{if $pageid == 1}
<!-- AB Test code for homepage goes here -->
{else}
<!-- regular code for other pages goes here -->
{/if}

6. Create a custom report in Google Analytics. You can do anything you like with this report really, but the key points are as follows:
- Select as many metrics as are relevant - maybe you want to test whether your new heading yields a lower bounce rate? Maybe you want to test how many newsletter signup goals your new subscription form yields?
- For the dimensions, add "Custom Variable Value X" (where X is the slot or index number you set previously for this test).