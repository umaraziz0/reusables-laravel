<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
  AlertCircle,
  ChevronDown,
  FormInput,
  SquarePower,
  Tag,
} from '@lucide/vue';

import { formInputField } from '@/actions/App/Http/Controllers/ComponentPreviewController';
// --- Types --- //
import {
  Collapsible,
  CollapsibleContent,
  CollapsibleTrigger,
} from '@/components/ui/collapsible';
// --- Components --- //
import {
  Sidebar,
  SidebarContent,
  SidebarGroup,
  SidebarGroupLabel,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/composables/useCurrentUrl';
import { alert, badges, buttons } from '@/routes/components';
import { type NavItem } from '@/types';

// --- Constants --- //

const componentNavItems: NavItem[] = [
  {
    title: 'Alert',
    href: alert(),
    icon: AlertCircle,
  },
  {
    title: 'Badges',
    href: badges(),
    icon: Tag,
  },
  {
    title: 'Buttons',
    href: buttons(),
    icon: SquarePower,
  },
  {
    title: 'Form',
    href: formInputField(),
    icon: FormInput,
    isFolder: true,
    subItems: [
      {
        title: 'Input Field',
        href: formInputField(),
        icon: FormInput,
      },
    ],
  },
];

// --- Hooks --- //

const { isCurrentUrl } = useCurrentUrl();
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="buttons()">
              <span class="ml-1 truncate text-sm font-semibold"
                >Components</span
              >
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
      <SidebarGroup class="px-2 py-0">
        <SidebarGroupLabel>Preview</SidebarGroupLabel>
        <SidebarMenu>
          <SidebarMenuItem v-for="item in componentNavItems" :key="item.title">
            <!-- Menu Folder -->
            <Collapsible v-if="item.isFolder" class="group/collapsible">
              <CollapsibleTrigger as-child>
                <SidebarMenuButton class="cursor-pointer" :tooltip="item.title">
                  <component :is="item.icon" />
                  <span>{{ item.title }}</span>
                  <ChevronDown
                    class="ml-auto transition-transform duration-200 group-data-[state=open]/collapsible:rotate-180"
                  />
                </SidebarMenuButton>
              </CollapsibleTrigger>

              <CollapsibleContent
                class="flex flex-col gap-1 py-1 pl-4 group-data-[collapsible=icon]:pl-0"
              >
                <SidebarMenuButton
                  as-child
                  v-for="subItem in item.subItems"
                  :is-active="isCurrentUrl(subItem.href)"
                  :key="subItem.title"
                >
                  <Link :href="subItem.href">
                    <component :is="subItem.icon" />
                    <span>{{ subItem.title }}</span>
                  </Link>
                </SidebarMenuButton>
              </CollapsibleContent>
            </Collapsible>

            <!-- Menu Item -->
            <SidebarMenuButton
              v-else
              as-child
              :is-active="isCurrentUrl(item.href)"
              :tooltip="item.title"
            >
              <Link :href="item.href">
                <component :is="item.icon" />
                <span>{{ item.title }}</span>
              </Link>
            </SidebarMenuButton>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarGroup>
    </SidebarContent>
  </Sidebar>
  <slot />
</template>
