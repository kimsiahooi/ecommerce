<script setup lang="ts">
import DropdownAction from '@/components/shared/DropdownAction.vue';
import { Pagination, type PaginateData } from '@/components/shared/pagination';
import { Select, type SelectOption } from '@/components/shared/select';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { DropdownMenu, DropdownMenuCheckboxItem, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { valueUpdater } from '@/components/ui/table/utils';
import { useDateFormat } from '@/composables/useDateFormat';
import AppLayout from '@/layouts/AppLayout.vue';
import AppMainLayout from '@/layouts/AppMainLayout.vue';
import type { BreadcrumbItem } from '@/types';
import type { ProductCategory } from '@/types/shop';
import { Head, Link, router } from '@inertiajs/vue3';
import type { ColumnDef, ColumnFiltersState, ExpandedState, SortingState, VisibilityState } from '@tanstack/vue-table';
import {
    FlexRender,
    getCoreRowModel,
    getExpandedRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useVueTable,
} from '@tanstack/vue-table';
import { ArrowUpDown, ChevronDown } from 'lucide-vue-next';
import { computed, h, reactive, ref, watch } from 'vue';

defineOptions({
    layout: AppMainLayout,
});

const props = defineProps<{
    product_categories: PaginateData<ProductCategory[]>;
}>();

const { formatDate } = useDateFormat();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
    {
        title: 'Product categories',
        href: route('shop.product-categories.index'),
    },
];

const selectOptions = <SelectOption[]>[
    { name: '10 Entries', value: '10' },
    { name: '25 Entries', value: '25' },
    { name: '50 Entries', value: '50' },
    { name: '100 Entries', value: '100' },
];

const routeParams = computed(() => route().params);

const setting = reactive({
    search: routeParams.value.search,
    entries: routeParams.value.entries || '10',
});

const deleteData = reactive<{
    dialogIsOpen: boolean;
    isDeleting: boolean;
    category: ProductCategory | null;
}>({
    dialogIsOpen: false,
    isDeleting: false,
    category: null,
});

const columns: ColumnDef<ProductCategory>[] = [
    {
        id: 'select',
        header: ({ table }) =>
            h(Checkbox, {
                modelValue: table.getIsAllPageRowsSelected() || (table.getIsSomePageRowsSelected() && 'indeterminate'),
                'onUpdate:modelValue': (value) => table.toggleAllPageRowsSelected(!!value),
                ariaLabel: 'Select all',
            }),
        cell: ({ row }) =>
            h(Checkbox, {
                modelValue: row.getIsSelected(),
                'onUpdate:modelValue': (value) => row.toggleSelected(!!value),
                ariaLabel: 'Select row',
            }),
        enableSorting: false,
        enableHiding: false,
    },
    {
        accessorKey: 'id',
        header: ({ column }) => {
            return h(
                Button,
                {
                    variant: 'ghost',
                    onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
                },
                () => ['ID', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
            );
        },
        cell: ({ row }) => h('div', { class: 'capitalize' }, row.getValue('id')),
    },
    {
        accessorKey: 'name',
        header: ({ column }) => {
            return h(
                Button,
                {
                    variant: 'ghost',
                    onClick: () => column.toggleSorting(column.getIsSorted() === 'asc'),
                },
                () => ['Name', h(ArrowUpDown, { class: 'ml-2 h-4 w-4' })],
            );
        },
        cell: ({ row }) => {
            const featureImage = row.original.feature_image;

            return h('div', { class: 'flex items-center gap-3' }, [
                featureImage ? h('img', { src: featureImage, alt: row.getValue('name'), class: 'size-10 object-cover' }) : null,
                h('p', null, row.getValue('name')),
            ]);
        },
    },
    {
        accessorKey: 'created_at',
        header: () => h('div', { class: 'text-center' }, 'Created At'),
        cell: ({ row }) => h('div', { class: 'text-center' }, formatDate(row.getValue('created_at'))),
        enableSorting: false,
    },
    {
        id: 'actions',
        enableHiding: false,
        header: () => h('div', { class: 'text-center' }, 'Action'),
        cell: ({ row }) => {
            const category = row.original;

            return h(
                'div',
                { class: 'text-center' },
                h(DropdownAction, {
                    actions: [
                        {
                            name: 'View Details',
                            link: {
                                url: route('shop.product-categories.show', category.id),
                            },
                        },
                        {
                            name: 'Edit',
                            link: {
                                url: route('shop.product-categories.edit', category.id),
                            },
                        },
                        {
                            name: 'Delete',
                            onClick: () => setDelete(category),
                            itemClass: '!bg-destructive !text-destructive-foreground',
                        },
                    ],
                    onExpand: row.toggleExpanded,
                }),
            );
        },
    },
];

const sorting = ref<SortingState>([]);
const columnFilters = ref<ColumnFiltersState>([]);
const columnVisibility = ref<VisibilityState>({});
const rowSelection = ref({});
const expanded = ref<ExpandedState>({});

const table = useVueTable({
    data: props.product_categories.data,
    columns,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    getSortedRowModel: getSortedRowModel(),
    getFilteredRowModel: getFilteredRowModel(),
    getExpandedRowModel: getExpandedRowModel(),
    onSortingChange: (updaterOrValue) => valueUpdater(updaterOrValue, sorting),
    onColumnFiltersChange: (updaterOrValue) => valueUpdater(updaterOrValue, columnFilters),
    onColumnVisibilityChange: (updaterOrValue) => valueUpdater(updaterOrValue, columnVisibility),
    onRowSelectionChange: (updaterOrValue) => valueUpdater(updaterOrValue, rowSelection),
    onExpandedChange: (updaterOrValue) => valueUpdater(updaterOrValue, expanded),
    state: {
        get sorting() {
            return sorting.value;
        },
        get columnFilters() {
            return columnFilters.value;
        },
        get columnVisibility() {
            return columnVisibility.value;
        },
        get rowSelection() {
            return rowSelection.value;
        },
        get expanded() {
            return expanded.value;
        },
        pagination: {
            pageIndex: 0,
            pageSize: props.product_categories.data.length,
        },
    },
});

const searchHandler = () => {
    router.visit(route('shop.product-categories.index', setting));
};

const resetHandler = () => {
    router.visit(route('shop.product-categories.index'));
};

const setDelete = (category: ProductCategory) => {
    deleteData.category = category;
};

const resetDelete = () => {
    deleteData.category = null;
};

const deleteHandler = () => {
    if (deleteData.category && !deleteData.isDeleting) {
        router.visit(route('shop.product-categories.destroy', deleteData.category.id), {
            method: 'delete',
            onBefore: () => {
                deleteData.isDeleting = true;
            },
            onFinish: () => {
                deleteData.isDeleting = false;
                deleteData.category = null;
            },
        });
    }
};

watch(
    () => setting.entries,
    () => {
        searchHandler();
    },
);

watch(
    () => deleteData.category,
    (newDelete) => {
        deleteData.dialogIsOpen = !!newDelete;
    },
);
</script>

<template>
    <Head title="Product Categories" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="w-full">
                <div class="flex flex-wrap items-center justify-end gap-2">
                    <Link :href="route('shop.products.create')" as-child>
                        <Button class="cursor-pointer">Create</Button>
                    </Link>
                </div>
                <div class="flex flex-col-reverse gap-2 py-4 md:flex-row md:items-center">
                    <form class="flex flex-col gap-2 md:flex-row md:items-center" @submit.prevent="searchHandler">
                        <div>
                            <Input class="min-w-60" placeholder="Search categories..." v-model="setting.search" />
                        </div>
                        <div class="flex gap-2">
                            <Button class="flex-1 cursor-pointer md:flex-auto" type="submit">Search</Button>
                            <Button class="flex-1 cursor-pointer md:flex-auto" type="reset" @click="resetHandler" variant="secondary">Reset</Button>
                        </div>
                    </form>
                    <div class="flex flex-1 flex-wrap items-center justify-end gap-2">
                        <Select :options="selectOptions" placeholder="Select Entries" v-model="setting.entries" />
                        <DropdownMenu>
                            <DropdownMenuTrigger as-child>
                                <Button variant="outline"> Columns <ChevronDown class="ml-2 h-4 w-4" /> </Button>
                            </DropdownMenuTrigger>
                            <DropdownMenuContent align="end">
                                <DropdownMenuCheckboxItem
                                    v-for="column in table.getAllColumns().filter((column) => column.getCanHide())"
                                    :key="column.id"
                                    class="capitalize"
                                    :model-value="column.getIsVisible()"
                                    @update:model-value="
                                        (value) => {
                                            column.toggleVisibility(!!value);
                                        }
                                    "
                                >
                                    {{ column.id.split('_').join(' ') }}
                                </DropdownMenuCheckboxItem>
                            </DropdownMenuContent>
                        </DropdownMenu>
                    </div>
                </div>
                <div class="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                                <TableHead v-for="header in headerGroup.headers" :key="header.id">
                                    <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header" :props="header.getContext()" />
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <template v-if="table.getRowModel().rows?.length">
                                <template v-for="row in table.getRowModel().rows" :key="row.id">
                                    <TableRow :data-state="row.getIsSelected() && 'selected'">
                                        <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                                            <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                                        </TableCell>
                                    </TableRow>
                                    <TableRow v-if="row.getIsExpanded()">
                                        <TableCell :colspan="row.getAllCells().length">
                                            {{ JSON.stringify(row.original) }}
                                        </TableCell>
                                    </TableRow>
                                </template>
                            </template>

                            <TableRow v-else>
                                <TableCell :colspan="columns.length" class="h-24 text-center"> No results. </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>

                <div class="flex flex-col items-center justify-end gap-2 space-x-2 py-4 md:flex-row">
                    <div class="text-muted-foreground flex-1 text-sm">
                        {{ table.getFilteredSelectedRowModel().rows.length }} of {{ table.getFilteredRowModel().rows.length }} row(s) selected.
                    </div>
                    <div class="space-x-2">
                        <Pagination :paginate-data="product_categories" />
                    </div>
                </div>
            </div>
        </div>
        <Dialog :open="deleteData.dialogIsOpen">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Delete {{ deleteData.category?.name }}</DialogTitle>
                    <DialogDescription> Are you sure you want to delete this category? </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button class="cursor-pointer" variant="secondary" @click="resetDelete">Cancel</Button>
                    <Button class="cursor-pointer" variant="destructive" :disabled="deleteData.isDeleting" @click="deleteHandler"> Delete </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
