<?php if (!defined('PmWiki')) exit();
/*  Copyright 2013 Michael Paulukonis
    This file is bootstrap-fluid.php; part of the bootstrap skin for pmwiki 2
    you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published
    by the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.
*/



global $RecipeInfo, $SkinName, $SkinRecipeName, $WikiStyleApply, $PageLogoUrl,
        $HTMLHeaderFmt, $PageHeaderFmt, $PageNavStyle, $UseDarkstrapTheme, $UseFlatUI,
        $PageEditForm, $PageTextStartFmt;
# Some Skin-specific values
$RecipeInfo['BootstrapSkin']['Version'] = '2013-05-20';
$SkinName = 'bootstrap-fluid';
$SkinRecipeName = "BootstrapSkin";

# for use in conditional markup  (:if enabled TriadSkin:)
global $BootstrapSkin; $BootstrapSkin = 1;

SDV($PageEditForm, "Bootstrap.EditForm");

$PageLogoUrl = "$SkinDirUrl/images/ico/favicon.png";


## from Hans Bracker's Triad skin (version 2008-07-10)
## automatic loading of skin default config pages
global $WikiLibDirs, $SkinDir;
$where = count($WikiLibDirs);
if ($where>1) $where--;
array_splice($WikiLibDirs, $where, 0,
             array(new PageStore("$SkinDir/wikilib.d/\$FullName")));

# attempt to set configs via actions....

global $Now, $CookiePrefix, $BootstrapThemeCookie, $BootstrapCoreCookie, $BootstrapNavbarCookie,
        $BootstrapTheme, $BootstrapCore, $BootstrapNav;

# set cookie expire time (default 1 year)
SDV($BootstrapCookieExpires,$Now+60*60*24*365);

$prefix = $CookiePrefix.$SkinName.'_';

SDV($SkinCookie, $prefix.'settheme');

# bootstrap cookie routine
# settheme changes the skin "permanently" (until cookie expires)
# theme temporarily changes the theme, but will revert to the cookie-settings next time
# setcore/core permanently/temporarily changes the core Bootstrapp
SDV($BootstrapThemeCookie, $prefix.'settheme');
SDV($BootstrapCoreCookie, $prefix.'setcore');

if (isset($_COOKIE[$BootstrapThemeCookie])) {
        $theme = $_COOKIE[$BootstrapThemeCookie];
}
if (isset($_GET['settheme'])) {
        $theme = $_GET['settheme'];
        setcookie($BootstrapThemeCookie, $theme, $BootstrapCookieExpires, '/');
}
if (isset($_GET['theme'])) {
        $theme = $_GET['theme'];
}
if (! isset($theme)) {
        $theme = $BootstrapTheme;
}


if (isset($_COOKIE[$BootstrapCoreCookie])) {
        $core = $_COOKIE[$BootstrapCoreCookie];
}
if (isset($_GET['setcore'])) {
        $core = $_GET['setcore'];
        setcookie($BootstrapCoreCookie, $core, $BootstrapCookieExpires, '/');
}
if (isset($_GET['core'])) {
        $core = $_GET['core'];
}
if (! isset($core)) {
        $core = $BootstrapCore;
}

# presence of navbar cookie will over-ride any defaults that may be set per theme
# ie, darkstrap and default bootstrap use inverse. because I think it looks better
if (isset($_COOKIE[$BootstrapNavbarCookie])) {
        $navbar = $_COOKIE[$BootstrapNavbarCookie];
}
if (isset($_GET['setnavbar'])) {
        $navbar = $_GET['setnavbar'];
        setcookie($BootstrapNavbarCookie, $navbar, $BootstrapCookieExpires, '/');
}
if (isset($_GET['navbar'])) {
        $navbar = $_GET['navbar'];
        $navbar = ($navbar == 'inverse' ? 'navbar-inverse' : '');
}
if (! isset($navbar)) {
        $navbar = $BootstrapNavbar;
        $navbar = ($navbar == 'inverse' ? 'navbar-inverse' : '');
}



### end cookies

if ($core == 'compass') {
        $HTMLHeaderFmt['core-css'] =
                "<link href='$SkinDirUrl/css/screen.css' rel='stylesheet'>";
} else {
        $HTMLHeaderFmt['core-css'] =
                "<link href='$SkinDirUrl/css/bootstrap.css' rel='stylesheet'>
         <link href='$SkinDirUrl/css/bootstrap-responsive.css' rel='stylesheet'>";
}

if ($theme == 'flatui') {
        $HTMLHeaderFmt['option-css'] =
                "<link href='$SkinDirUrl/css/flat-ui.css' rel='stylesheet'>";

        if (! isset($navbar)) $navbar = 'navbar-inverse';

} else if ($theme =='bootstrap') {

        $HTMLHeaderFmt['option-css'] = "";

        if (! isset($navbar)) $navbar = '';

} else {

        ## check for existence of file $theme.cs and pmwiki.$theme.css
        ## use if the first one exists
        ## otherwise use the default bootstrap

        if (file_exists("$FarmD/pub/skins/bootstrap-fluid/css/$theme.css")) {

            $HTMLHeaderFmt['option-css'] =
                "<link href='$SkinDirUrl/css/$theme.css' rel='stylesheet'>";

        } else {

            $HTMLHeaderFmt['option-css'] = "";
        }

        if (file_exists("$FarmD/pub/skins/bootstrap-fluid/css/pmwiki.$theme.css")) {

            $HTMLHeaderFmt['option-css2'] =
                "<link href='$SkinDirUrl/css/pmwiki.$theme.css' rel='stylesheet'>";

        } else {

            $HTMLHeaderFmt['option-css2'] = "";
        }

        if (! isset($navbar)) $navbar = '';

}


$PageNavStyle =
        "<div id='wikihead' class='navbar $navbar navbar-fixed-top'>";

$HTMLHeaderFmt['end-css'] =
        "<link href='$SkinDirUrl/css/pmwiki.css' rel='stylesheet' />";


## required for apply-actions
$WikiStyleApply['link'] = 'a';  #allows A to be labelled with class attributes


Markup('button', 'links',
       '/\\(:button(\\s+.*?)?:\\)/ei',
       "Keep(BootstrapButton(PSS('$1 ')), 'L')");

function BootstrapButton($args) {

        $opt = ParseArgs($args);

        // expect link, class

        // TODO: test for options
        // TODO: handle alt params
        // TODO: handle rel=nofollow per pmwiki settings
        // what about other PmWiki shortcut-type things?
        // like... [[PmWiki/basic editing|+]]%apply=link class="btn"%

        $target = $opt['link'];
        $text = $opt['text'] ? $opt['text'] : $target; // if text not provided, default to the link
        $class = $opt['class'];

        $l = '<a href="%s" class="%s">%s</a>';
        $linkf = sprintf($l, $target, $class, $text);

        return $linkf;

}

# the markup seems to work -- it's just that the CSS isn't finding the icon set...

Markup('icon', 'inline',
       '/\\(:icon(\\s+.*?)?:\\)/ei',
       "BootstrapIcon(PSS('$1 '))");

function BootstrapIcon($args) {

        $icon = sprintf('<i class=%s ></i>', $args);

        return $icon;
}


