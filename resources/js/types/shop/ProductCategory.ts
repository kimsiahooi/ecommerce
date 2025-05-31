export interface ProductCategory {
    readonly id: number;
    name: string;
    description: string | null;
    slug: string;
    feature_image: string | null;
    created_at: Date | null;
    updated_at: Date | null;
    deleted_at: Date | null;
}
