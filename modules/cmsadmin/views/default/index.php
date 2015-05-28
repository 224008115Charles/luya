<script type="text/ng-template" id="reverse.html">
    <a class="treeview__button btn-flat [ waves-effect waves-teal ]" ng-click="go(data.id)">{{data.title}}</a>
    <ul class="treeview__list" role="menu">
        <li class="treeview__item" role="menuitem" ng-repeat="data in data.nodes" ng-include="'reverse.html'"></li>
    </ul>
</script>

<script type="text/ng-template" id="createform.html">
<div ng-switch on="data.nav_item_type" class="card-panel">
<h5>Neue Seite hinzufügen</h5>
<div class="row">
    <div class="input-field col s6">
        <input type="text" ng-model="data.title" />
        <label>Seiten Titel</label>
    </div>
    <div class="input-field col s6">
        <input type="text" ng-model="data.rewrite" />
        <label>Rewrite</label>
    </div>
</div>
<div class="row" ng-show="!data.nav_id">
    <div class="col s12">
        <label>Kategorrie</label>
        <select class="browser-default" ng-model="data.cat_id" ng-options="item.id as item.name for item in cat" />
    </div>
</div>
<div class="row">
    <div class="col s12">
        <label>Sprache</label>        
        <select class="browser-default" ng-model="data.lang_id" ng-options="item.id as item.name for item in lang" />
    </div>
</div>
<div class="row">
    <div class="col s12">
        <label>Navigations Punkt von</label>
        <select class="browser-default" ng-model="data.parent_nav_id">
            <option value="0">[Root Level]</option>
            <option ng-repeat="nav in navitems" value="{{nav.id}}">{{nav.title}}</option>
        </select>
    </div>
</div>
<div class="row">
    <div class="col s12">
        <p><input type="radio" ng-model="data.nav_item_type" value="1" id="t1"><label for="t1">Seite</label></p>
        <p><input type="radio" ng-model="data.nav_item_type" value="2" id="t2"><label for="t2">Module</label></p>
        <p><input type="radio" ng-model="data.nav_item_type" value="3" id="t3"><label for="t3">Weiterleitung</label></p>
    </div>
</div>
<div ng-switch-when="1">
    <create-form-page data="data"></create-form-page>
</div>

<div ng-switch-when="2">
    <create-form-module data="data"></create-form-module>
</div>

<div ng-switch-when="3">
    Redir
</div>

<div ng-switch-when="true">
    <p>Diese Seite wurde erfolgreich erstellt!
</div>

</div>
</script>

<script type="text/ng-template" id="createformpage.html">
<div class="row">
    <div class="col s12">
        <label>Layout</label>
        <select class="browser-default" ng-model="data.layout_id" ng-options="lts.id as lts.name for lts in layouts"></select>
    </div>
</div>
<div class="row">
    <div class="col s12">
        <button type="button" class="btn" ng-click="save()">Neue Seite Speichern</button>
    </div>
</div>
</script>

<script type="text/ng-template" id="createformmodule.html">
<div class="row">
    <div class="col s12 input-field">
        <input type="text" ng-model="data.module_name" />
        <label>Module Name (Yii-ID)</label>
    </div>
</div>
<div class="row">
    <div class="col s12">
        <button type="button" class="btn" ng-click="save()">Neue Seite Speichern</button>
    </div>
</div>
</script>

<!--<a class="btn-floating btn-large waves-effect waves-light red" style="position:absolute; margin-left:334px; margin-top:20px;" ui-sref="custom.cmsadd"><i class="mdi-content-add"></i></a>-->
<div class="luya-container__sidebar">
    <div class="row">
        <div class="col s12">
            <div ng-controller="CmsMenuTreeController">
                <div class="treeview" ng-repeat="catitem in menu">
                    <h5 class="treeview__title">{{catitem.name}}</h5>
                    <ul class="treeview__list">
                        <li class="treeview__item" ng-repeat="data in catitem.__items" ng-include="'reverse.html'"></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>  <!-- /.luya-sidebar -->

<div class="luya-container__main">

    <div class="col s12">
        <div ui-view></div>
    </div>

</div>  <!-- /.luya-main -->