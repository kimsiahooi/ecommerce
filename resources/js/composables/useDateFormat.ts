import { format } from 'date-fns';

export const useDateFormat = () => {
    const formatDate = (datetime: Date, formatString = 'yyyy-MM-dd, h:mm aaa') => format(datetime, formatString);
    return { formatDate };
};
