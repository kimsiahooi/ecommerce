<script setup lang="ts">
import DropdownAction from '@/components/shared/DropdownAction.vue';
import Pagination from '@/components/shared/Pagination.vue';
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
import type { PaginateData } from '@/types/PaginateData';
import type { Product } from '@/types/Product';
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
import { h, reactive, ref, watch } from 'vue';

defineOptions({
    layout: AppMainLayout,
});

const props = defineProps<{
    products: PaginateData<Product[]>;
}>();

const { formatDate } = useDateFormat();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: route('dashboard'),
    },
    {
        title: 'Products',
        href: route('products.index'),
    },
];

const setting = reactive({
    search: route().params.search,
});

const deleteProduct = reactive<{
    dialogIsOpen: boolean;
    isDeleting: boolean;
    product: Product | null;
}>({
    dialogIsOpen: false,
    isDeleting: false,
    product: null,
});

const columns: ColumnDef<Product>[] = [
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
        accessorKey: 'price',
        header: () => h('div', { class: 'text-center' }, 'Price'),
        cell: ({ row }) => h('div', { class: 'text-center' }, row.getValue('price')),
        enableSorting: false,
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
            const product = row.original;

            return h(
                'div',
                { class: 'text-center' },
                h(DropdownAction, {
                    actions: [
                        {
                            name: 'View Details',
                            link: {
                                url: route('products.show', product.id),
                            },
                        },
                        {
                            name: 'Edit Product',
                            link: {
                                url: route('products.edit', product.id),
                            },
                        },
                        {
                            name: 'Delete Product',
                            onClick: () => setDeleteProduct(product),
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
    data: props.products.data,
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
            pageSize: props.products.data.length,
        },
    },
});

const searchHandler = () => {
    router.visit(route('products.index', setting));
};

const resetHandler = () => {
    router.visit(route('products.index'));
};

const setDeleteProduct = (product: Product) => {
    deleteProduct.product = product;
};

const resetDeleteProduct = () => {
    deleteProduct.product = null;
};

const deleteProductHandler = () => {
    if (deleteProduct.product) {
        router.visit(route('products.destroy', { product: deleteProduct.product?.id }), {
            method: 'delete',
            onBefore: () => {
                deleteProduct.isDeleting = true;
            },
            onFinish: () => {
                deleteProduct.isDeleting = false;
                deleteProduct.product = null;
            },
        });
    }
};

watch(
    () => deleteProduct.product,
    (newDeleteProduct) => {
        deleteProduct.dialogIsOpen = !!newDeleteProduct;
    },
);
</script>

<template>
    <Head title="Products" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="w-full">
                <div class="flex flex-wrap items-center justify-end gap-2">
                    <Link :href="route('products.create')" as-child>
                        <Button class="cursor-pointer">Create</Button>
                    </Link>
                </div>
                <div class="flex items-center py-4">
                    <form class="flex items-center gap-2" @submit.prevent="searchHandler">
                        <Input class="min-w-60" placeholder="Search products..." v-model="setting.search" />
                        <Button class="cursor-pointer" type="submit">Search</Button>
                        <Button class="cursor-pointer" type="reset" @click="resetHandler" variant="secondary">Reset</Button>
                    </form>
                    <DropdownMenu>
                        <DropdownMenuTrigger as-child>
                            <Button variant="outline" class="ml-auto"> Columns <ChevronDown class="ml-2 h-4 w-4" /> </Button>
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

                <div class="flex items-center justify-end space-x-2 py-4">
                    <div class="text-muted-foreground flex-1 text-sm">
                        {{ table.getFilteredSelectedRowModel().rows.length }} of {{ table.getFilteredRowModel().rows.length }} row(s) selected.
                    </div>
                    <div class="space-x-2">
                        <Pagination :paginate-data="products" />
                    </div>
                </div>
            </div>
        </div>
        <Dialog :open="deleteProduct.dialogIsOpen">
            <DialogContent class="sm:max-w-[425px]">
                <DialogHeader>
                    <DialogTitle>Delete {{ deleteProduct.product?.name }}</DialogTitle>
                    <DialogDescription> Are you sure you want to delete this product? </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button class="cursor-pointer" variant="secondary" @click="resetDeleteProduct">Cancel</Button>
                    <Button class="cursor-pointer" variant="destructive" :disabled="deleteProduct.isDeleting" @click="deleteProductHandler">
                        Delete
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
