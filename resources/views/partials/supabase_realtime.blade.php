<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const supabaseUrl = '{{ config('services.supabase.url') }}';
        const supabaseKey = '{{ config('services.supabase.key') }}';

        if (supabaseUrl && supabaseKey) {
            const supabase = window.supabase.createClient(supabaseUrl, supabaseKey);
            
            // Helper to check if event matches current user's students (for Parents)
            const isRelevantToMyStudents = (payload) => {
                if (!window.myStudentIds || !Array.isArray(window.myStudentIds)) return false;
                const studentId = payload.new?.student_id || payload.old?.student_id;
                return window.myStudentIds.some(id => id == studentId);
            };

            // Listen to Fees channel
            supabase.channel('realtime:fees')
                .on('postgres_changes', { event: '*', schema: 'public', table: 'student_fees' }, payload => {
                    console.log('Realtime fee update:', payload);
                    
                    // Admin/Staff Logic
                    if (typeof fetchMetrics === 'function' && !window.myStudentIds) fetchMetrics();
                    if (typeof fetchStudentList === 'function') fetchStudentList();
                    if (window.location.href.includes('admin/fees') || window.location.href.includes('staff/dashboard')) window.location.reload();

                    // Parent Logic
                    if (isRelevantToMyStudents(payload)) {
                        if (typeof window.fetchMetrics === 'function') window.fetchMetrics();
                        if (window.location.href.includes('parent/soa') || window.location.href.includes('parent/fees')) window.location.reload();
                    }
                })
                .subscribe();

            // Listen to Payments channel
            supabase.channel('realtime:payments')
                .on('postgres_changes', { event: '*', schema: 'public', table: 'payments' }, payload => {
                    console.log('Realtime payment update:', payload);
                    
                    // Admin/Staff Logic
                    if (typeof fetchMetrics === 'function' && !window.myStudentIds) fetchMetrics();
                    if (typeof fetchStudentList === 'function') fetchStudentList();
                    if (window.location.href.includes('admin/payment_approvals') || window.location.href.includes('staff/dashboard')) window.location.reload();

                    // Parent Logic
                    if (isRelevantToMyStudents(payload)) {
                        if (typeof window.fetchMetrics === 'function') window.fetchMetrics();
                        if (window.location.href.includes('parent/history') || window.location.href.includes('parent/soa')) window.location.reload();
                    }
                })
                .subscribe();

            // Listen to SMS Logs channel
            supabase.channel('realtime:sms_logs')
                .on('postgres_changes', { event: '*', schema: 'public', table: 'sms_logs' }, payload => {
                    console.log('Realtime SMS log update:', payload);
                    
                    if (window.location.href.includes('admin/sms/logs')) {
                        if (payload.eventType === 'INSERT') {
                             // New log entry, reload to show it
                             // Optional: Show a toast notification "New SMS Logged"
                             window.location.reload(); 
                        } else {
                             // Update existing, trigger event for Alpine to pick up
                             window.dispatchEvent(new CustomEvent('sms-log-updated', { detail: payload }));
                        }
                    }
                })
                .subscribe();
        } else {
            console.warn('Supabase credentials missing in config.');
        }
    });
</script>
