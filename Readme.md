# TYPO3 Extension: All mails to me

## What does it do?

* You as a Backend user receive **all** mails you generate, e.g. by filling forms in the frontend.
* Mails are only sent to you and **not** to the original recipients. 
* The mail subject is suffixed with the original recipient addresses. (target to get changed)

## Why use it?

* Real recipients don't receive your test mails
* No ugly test-configuration in forms and such needed

## How to use it?

In the top right corner of the TYPO3 Backend you have a toolbar item with a letter icon that toggles the functionality.

The state is saved to your session, so you need to activate it after every login.

Non-Admin users need the TSconfig `tx_allmailstome.enabled = 1`.

## Current State

The current state is "preview", means: use it, test it, have fun and please create issues when something is not okay.