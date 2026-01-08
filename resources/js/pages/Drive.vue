<script setup lang="ts">
import DriveController from '@/actions/App/Http/Controllers/DriveController';
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuGroup,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import { File, Folder } from '@/types';
import { Form, Head, Link, useForm } from '@inertiajs/vue3';
import { Folder as FolderIcon } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps<{
    folders: Array<Folder>;
    files: Array<File>;
}>();

const openCreateFolder = ref(false);
const openAddFile = ref(false);

const form = useForm({
    name: '',
    description: '',

    title: '',
    file_name: '',

    folder_id: '',
});

function handleAddFile(e: any) {
    form.file_name = e.target?.files[0];
}

const createFolder = () => {
    form.post(DriveController.store.url(), {
        onSuccess: () => {
            console.log('success');
            openCreateFolder.value = false;
            form.reset();
        },
    });
};
const createFile = () => {
    form.post(DriveController.store.url(), {
        forceFormData: true,
        onSuccess: () => {
            console.log('success');
            openAddFile.value = false;
            form.reset();
        },
    });
};
</script>
<template>
    <AppLayout>
        <Head title="Drive" />
        <div class="flex flex-1 flex-col p-4">
            <div class="flex items-center justify-between">
                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button> New </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="start">
                        <DropdownMenuGroup>
                            <DropdownMenuItem @click="openAddFile = true">
                                New File
                            </DropdownMenuItem>
                            <DropdownMenuItem @click="openCreateFolder = true">
                                New Folder
                            </DropdownMenuItem>
                        </DropdownMenuGroup>
                    </DropdownMenuContent>
                </DropdownMenu>

                <Input class="w-xl" />
            </div>

            <div class="mt-10 flex flex-col gap-10">
                <section>
                    <Heading title="Folders" />

                    <div class="grid grid-cols-4 gap-4">
                        <div v-for="folder in props.folders">
                            <Link :href="`/drive/${folder.id}`">
                                <Card class="p-2">
                                    <CardHeader class="p-2">
                                        <div class="flex items-center gap-4">
                                            <FolderIcon />
                                            <div>
                                                <CardTitle>{{
                                                    folder.name
                                                }}</CardTitle>
                                                <CardDescription>{{
                                                    new Date(
                                                        folder.created_at,
                                                    ).toLocaleString()
                                                }}
                                                </CardDescription>
                                            </div>
                                        </div>
                                    </CardHeader>
                                </Card>
                            </Link>
                        </div>
                        <p v-if="folders.length === 0">No folders added</p>
                    </div>
                </section>
                <section>
                    <Heading title="Files" />
                    <div class="grid grid-cols-4 gap-4">
                        <div v-for="file in props.files" >
                            <Card class="min-h-24 p-2">
                                <CardHeader class="p-2">
                                    <CardTitle class="truncate">{{
                                        file.title
                                    }}</CardTitle>

                                    <CardDescription class="truncate">
                                        <a
                                            :href="`/storage/${file.file_name}`"
                                            target="_blank"
                                            >{{ file.file_name }}</a
                                        >
                                        <p>{{ file.folder?.name }}</p>
                                        </CardDescription
                                    >
                                </CardHeader>
                            </Card>
                        </div>
                    </div>
                    <p v-if="files.length === 0">No file added</p>
                </section>
            </div>
            <Dialog v-model:open="openCreateFolder">
                <DialogContent>
                    <Form
                        @submit.prevent="createFolder"
                        v-slot="{ errors, processing }"
                        class="space-y-6"
                    >
                        <DialogHeader>
                            <DialogTitle>Create Folder</DialogTitle>
                            <DialogDescription>
                                Enter the folder name and description to create
                                a new folder in your drive.
                            </DialogDescription>
                        </DialogHeader>
                        <div class="space-y-4">
                            <Label for="folder-name">Folder name</Label>
                            <Input
                                type="text"
                                id="folder-name"
                                v-model="form.name"
                                for
                                placeholder="Folder name"
                                required
                            />
                        </div>
                        <DialogFooter>
                            <DialogClose as-child>
                                <Button variant="ghost">Close</Button>
                            </DialogClose>
                            <Button type="submit">Create Folder</Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>
            <Dialog v-model:open="openAddFile">
                <DialogContent>
                    <Form
                        @submit.prevent="createFile"
                        v-slot="{ errors, processing }"
                        class="space-y-6"
                    >
                        <DialogHeader>
                            <DialogTitle>Add File</DialogTitle>
                            <DialogDescription>
                                Enter the file name and file to create a new
                                file in your drive.
                            </DialogDescription>
                        </DialogHeader>
                        <div class="space-y-4">
                            <Label for="add-to-folder">Add to</Label>
                            <Select
                                v-model="form.folder_id"
                                :disabled="folders.length === 0"
                            >
                                <SelectTrigger class="w-full">
                                    <SelectValue
                                        :placeholder="
                                            folders.length > 0
                                                ? 'Select a folder'
                                                : 'No folder available'
                                        "
                                    />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem
                                        v-for="folder in folders"
                                        :key="folder.id"
                                        :value="folder.id"
                                        >{{ folder.name }}</SelectItem
                                    >
                                </SelectContent>
                            </Select>

                            <Label for="file-title">File title</Label>
                            <Input
                                type="text"
                                id="file-title"
                                v-model="form.title"
                                placeholder="file name"
                            />
                            <InputError :message="form.errors.title" />
                            <Label for="file-description"
                                >file description</Label
                            >
                            <Input
                                type="text"
                                id="file-description"
                                v-model="form.description"
                                placeholder="file description"
                            />
                            <InputError :message="form.errors.description" />
                            <Label for="file-name">file description</Label>
                            <Input
                                type="file"
                                @change="handleAddFile"
                                id="file-name"
                            />
                            <InputError :message="form.errors.file_name" />
                        </div>
                        <DialogFooter>
                            <DialogClose as-child>
                                <Button variant="ghost">Close</Button>
                            </DialogClose>
                            <Button type="submit">Create Folder</Button>
                        </DialogFooter>
                    </Form>
                </DialogContent>
            </Dialog>
        </div>
    </AppLayout>
</template>
