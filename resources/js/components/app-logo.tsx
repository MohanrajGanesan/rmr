import { FileText } from 'lucide-react';

export default function AppLogo() {
    return (
        <>
            <div className="flex aspect-square size-8 items-center justify-center rounded-md bg-zinc-800 text-white dark:bg-zinc-700 dark:text-white">
                <FileText className="size-5" />
            </div>

            <div className="ml-1 grid flex-1 text-left text-sm">
                <span className="truncate leading-tight font-semibold">
                    RMR
                </span>
            </div>
        </>
    );
}