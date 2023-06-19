<?php

// // Make 'magic' roles, which will allow $w to serve API content,
// // But which are never navigated by a logged in user.
// // These are indicated by a prefix/postfix pattern:

// // // token_policy_[DescriptorBuiltbyMyTokenRolesHook]_allowed

// function token_policy_[DescriptorBuiltbyMyTokenRolesHook]_allowed(Web $w,$path) {
//     return $w->checkUrl($path, "MyUsefulAPIModule", "MyUsefulAPIModuleActions", "MyAction")
//     || $w->checkUrl($path, "MyUsefulAPIModule", "MyUsefulAPIModuleActions", "MyOtherAction")
//     || $w->checkUrl($path, "MyUsefulAPIModule", "MyUsefulAPIModuleActions", "MyAlternateAction");
// }


