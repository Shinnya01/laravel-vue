<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import Button from '@/components/ui/button/Button.vue';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

import InputError from '@/components/InputError.vue';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app/AppHeaderLayout.vue';
import task, { store } from '@/routes/task';
import { Task } from '@/types';
import { Form, Head, useForm } from '@inertiajs/vue3';
import { Pencil, Trash } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import TaskController from '@/actions/App/Http/Controllers/TaskController';

const props = defineProps<{
    tasks: Array<{
        id: number;
        title: string;
        description: string;
        status: string;
    }>;
}>();

const selectedTask = ref<Task | null>(null);

const openCreate = ref(false);
const openEdit = ref(false);
const openDelete = ref(false);

const form = useForm({
    title: '',
    description: '',
});

const doneTask = (task: Task) => {
    form.post(`/task/markAsDone/${task.id} `, {
        onSuccess: () => {
            console.log('done');
        },
    });
};

const createTask = () => {
    form.post(store.url(), {
        onSuccess: () => {
            form.reset();
            openCreate.value = false;
        },
    });
};

const editTask = () => {
    if (!selectedTask.value) return;
    form.put(`/task/update/${selectedTask.value.id}`, {
        onSuccess: () => {
            openEdit.value = false;
            form.reset();
            selectedTask.value = null;
        },
    });
};

const deleteTask = () => {
    if (!selectedTask.value) return;
    form.delete(`/task/delete/${selectedTask.value.id}`, {
        onSuccess: () => {
            form.reset();
            openDelete.value = false;
        },
    });
};

const openCreateDialog = () => {
    selectedTask.value = null;
    form.reset();
    openCreate.value = true;
};

const openEditDialog = (task: Task) => {
    selectedTask.value = task;

    form.title = task.title;
    form.description = task.description;

    openEdit.value = true;
};

const openDeleteDialog = (task: Task) => {
    selectedTask.value = task;

    openDelete.value = true;
};
const tasks = ref([...props.tasks]);

const ongoingTasks = computed(() =>
    tasks.value.filter((t) => t.status === 'ongoing'),
);
const doneTasks = computed(() =>
    tasks.value.filter((t) => t.status === 'done'),
);
</script>
<template>
    <Head title="Task" />
    <AppLayout>
        <div class="mx-auto flex w-4xl flex-1 flex-col p-4">
            <div class="flex justify-between">
                <Heading title="Task" />
                <Dialog v-model:open="openCreate">
                    <DialogTrigger as-child>
                        <Button>Create</Button>
                    </DialogTrigger>

                    <DialogContent>
                        <Form
                            v-bind="TaskController.store.form()"
                            v-slot="{ errors }"
                            class="space-y-4"
                        >
                            <DialogHeader>
                                <DialogTitle>New Task</DialogTitle>
                            </DialogHeader>

                            <div class="space-y-4">
                                <Label for="task-title">Task Title</Label>
                                <Input
                                    id="task-title"
                                    placeholder="Enter Task title"
                                    name="title"
                                />
                                <InputError :message="errors.title" />
                            </div>
                            <div class="space-y-4">
                                <Label for="task-description"
                                    >Task Description</Label
                                >
                                <Input
                                    id="task-description"
                                    placeholder="Enter Task description"
                                    v-model="form.description"
                                />
                                <InputError
                                    :message="errors.description"
                                />
                            </div>
                            <DialogFooter>
                                <DialogClose as-child>
                                    <Button variant="outline"> Cancel </Button>
                                </DialogClose>
                                <Button type="submit"> Save changes </Button>
                            </DialogFooter>
                        </Form>
                    </DialogContent>
                </Dialog>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <section class="rounded-lg border p-4">
                    <HeadingSmall title="Ongoing" />
                    <div v-for="task in ongoingTasks">
                        <Card class="my-2">
                            <CardHeader>
                                <div class="flex items-center justify-between">
                                    <CardTitle>{{ task.title }}</CardTitle>
                                    <Button @click="doneTask(task)"
                                        >Done</Button
                                    >
                                </div>
                                <CardDescription>{{
                                    task.description
                                }}</CardDescription>
                            </CardHeader>
                            <CardContent class="">
                                <div class="flex gap-2">
                                    <Pencil
                                        class="size-4"
                                        @click="openEditDialog(task)"
                                    />
                                    <Trash
                                        class="size-4 text-red-500"
                                        @click="openDeleteDialog(task)"
                                    />
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                    <div v-if="ongoingTasks.length <= 0">No task</div>
                </section>
                <section class="rounded-lg border p-4">
                    <HeadingSmall title="Done" />
                    <div v-for="task in doneTasks">
                        <Card class="my-2">
                            <CardHeader>
                                <div class="flex items-center justify-between">
                                    <CardTitle>{{ task.title }}</CardTitle>
                                    <Button @click="doneTask(task)"
                                        >Done</Button
                                    >
                                </div>
                                <CardDescription>{{
                                    task.description
                                }}</CardDescription>
                            </CardHeader>
                            <CardContent class="">
                                <div class="flex gap-2">
                                    <Pencil
                                        class="size-4"
                                        @click="openEditDialog(task)"
                                    />
                                    <Trash
                                        class="size-4 text-red-500"
                                        @click="openDeleteDialog(task)"
                                    />
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                    <div v-if="doneTasks.length <= 0">No task</div>
                </section>
            </div>
        </div>
        <Dialog v-model:open="openEdit">
            <DialogContent>
                <Form @submit.prevent="editTask" class="space-y-4">
                    <DialogHeader>
                        <DialogTitle>Edit Task</DialogTitle>
                    </DialogHeader>

                    <div class="space-y-4">
                        <Label for="task-title">Task Title</Label>
                        <Input
                            id="task-title"
                            placeholder="Enter Task title"
                            v-model="form.title"
                        />
                        <InputError :message="form.errors.title" />
                    </div>
                    <div class="space-y-4">
                        <Label for="task-description">Task Description</Label>
                        <Input
                            id="task-description"
                            placeholder="Enter Task description"
                            v-model="form.description"
                        />
                        <InputError :message="form.errors.description" />
                    </div>
                    <DialogFooter>
                        <DialogClose as-child>
                            <Button variant="outline"> Cancel </Button>
                        </DialogClose>
                        <Button type="submit"> Save changes </Button>
                    </DialogFooter>
                </Form>
            </DialogContent>
        </Dialog>
        <Dialog v-model:open="openDelete">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle
                        >Delete
                        {{ selectedTask?.title ?? 'Unknown' }}</DialogTitle
                    >
                    <DialogDescription>
                        This action cannot be undone. Are you sure you want to
                        permanently delete this file from our servers?
                    </DialogDescription>
                </DialogHeader>
                <DialogFooter>
                    <Button type="submit" @click="deleteTask"> Confirm </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </AppLayout>
</template>
