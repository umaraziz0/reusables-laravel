<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { AlertCircle, FormInput, SquarePower, Tag } from '@lucide/vue';

import { formInputField } from '@/actions/App/Http/Controllers/ComponentPreviewController';
// --- Types --- //
import {
  Accordion,
  AccordionItem,
  AccordionTrigger,
  AccordionContent,
} from '@/components/ui/accordion';
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
            <Accordion v-if="item.isFolder" type="single" collapsible>
              <AccordionItem :value="item.title" class="border-b-0">
                <AccordionTrigger
                  class="cursor-pointer gap-2 py-0 font-light text-[#4D4D4D] hover:no-underline"
                >
                  <SidebarMenuButton
                    as-child
                    :is-active="false"
                    :tooltip="item.title"
                  >
                    <div class="flex items-center gap-2">
                      <component :is="item.icon" />
                      <span>{{ item.title }}</span>
                    </div>
                  </SidebarMenuButton>
                </AccordionTrigger>

                <AccordionContent class="flex flex-col gap-1 py-1 pl-4">
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
                </AccordionContent>
              </AccordionItem>
            </Accordion>

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
