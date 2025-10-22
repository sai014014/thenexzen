<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Bug extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'type',
        'reported_by',
        'assigned_to',
        'steps_to_reproduce',
        'expected_behavior',
        'actual_behavior',
        'resolution_notes',
        'resolved_at'
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // Status constants
    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_TESTING = 'testing';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    // Priority constants
    const PRIORITY_LOW = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_CRITICAL = 'critical';

    // Type constants
    const TYPE_BUG = 'bug';
    const TYPE_FEATURE_REQUEST = 'feature_request';
    const TYPE_IMPROVEMENT = 'improvement';
    const TYPE_TASK = 'task';

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeResolved($query)
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    public function scopeClosed($query)
    {
        return $query->where('status', self::STATUS_CLOSED);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Accessors
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'open' => '<span class="badge bg-warning">Open</span>',
            'in_progress' => '<span class="badge bg-info">In Progress</span>',
            'testing' => '<span class="badge bg-primary">Testing</span>',
            'resolved' => '<span class="badge bg-success">Resolved</span>',
            'closed' => '<span class="badge bg-secondary">Closed</span>'
        ];

        return $badges[$this->status] ?? '<span class="badge bg-light">Unknown</span>';
    }

    public function getPriorityBadgeAttribute()
    {
        $badges = [
            'low' => '<span class="badge bg-success">Low</span>',
            'medium' => '<span class="badge bg-warning">Medium</span>',
            'high' => '<span class="badge bg-danger">High</span>',
            'critical' => '<span class="badge bg-dark">Critical</span>'
        ];

        return $badges[$this->priority] ?? '<span class="badge bg-light">Unknown</span>';
    }

    public function getTypeBadgeAttribute()
    {
        $badges = [
            'bug' => '<span class="badge bg-danger">Bug</span>',
            'feature_request' => '<span class="badge bg-info">Feature Request</span>',
            'improvement' => '<span class="badge bg-primary">Improvement</span>',
            'task' => '<span class="badge bg-secondary">Task</span>'
        ];

        return $badges[$this->type] ?? '<span class="badge bg-light">Unknown</span>';
    }

    // Helper methods
    public function isOpen()
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function isInProgress()
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isResolved()
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    public function isClosed()
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function getDaysOpen()
    {
        if ($this->resolved_at) {
            return $this->created_at->diffInDays($this->resolved_at);
        }
        return $this->created_at->diffInDays(now());
    }

    // Relationships
    public function attachments()
    {
        return $this->hasMany(BugAttachment::class);
    }
}
