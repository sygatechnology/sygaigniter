import React from 'react';

import { SidebarMenu } from './../../components';

export const SidebarMiddleNav = () => (
    <SidebarMenu>
        { /* -------- Tableau de bord ---------*/ }
        <SidebarMenu.Item
            icon={<i className="fa fa-fw fa-home"></i>}
            title="Tableau de bord"
            to='/'
        />

        { /* -------- Articles ---------*/ }
        <SidebarMenu.Item
            icon={<i className="fa fa-fw far fa-file"></i>}
            title="Articles"
        >
            <SidebarMenu.Item title="Tous les articles" to='/posts' exact />
            <SidebarMenu.Item title="Ajouter" to='/posts?page=new' exact />
            <SidebarMenu.Item title="Catégories" to='/post-tags?taxonomy=category' exact />
            <SidebarMenu.Item title="Etiquettes" to='/post-tags?taxonomy=tag' exact />
        </SidebarMenu.Item>

        { /* -------- Pages ---------*/ }
        <SidebarMenu.Item
            icon={<i className="fa fa-fw fa-copy"></i>}
            title="Pages"
        >
            <SidebarMenu.Item title="Toutes les pages" to='/posts?type=page' exact />
            <SidebarMenu.Item title="Ajouter" to='/posts?type=page&page=new' exact />
        </SidebarMenu.Item>

        { /* -------- Pages ---------*/ }
        <SidebarMenu.Item
            icon={<i className="fa fa-fw fa-plug"></i>}
            title="Extensions"
        >
            <SidebarMenu.Item title="Extensions installées" to='/plugins' exact />
            <SidebarMenu.Item title="Ajouter" to='/plugins?page=install' exact />
        </SidebarMenu.Item>

        { /* -------- Pages ---------*/ }
        <SidebarMenu.Item
            icon={<i className="fa fa-fw fa-user"></i>}
            title="Utilisateurs"
        >
            <SidebarMenu.Item title="Tous les utilisateurs" to='/users' exact />
            <SidebarMenu.Item title="Votre profil" to='/profile' exact />
        </SidebarMenu.Item>
    </SidebarMenu >
);
